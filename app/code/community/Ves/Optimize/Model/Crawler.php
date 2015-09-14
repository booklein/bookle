<?php
/**
 * Magento Ves Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Ves Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/ves-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Ves
 * @package     Ves_Optimize
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/ves-edition
 */

/**
 * Enter description here ...
 *
 * @method Ves_Optimize_Model_Resource_Crawler _getResource()
 * @method Ves_Optimize_Model_Resource_Crawler getResource()
 * @method int getStoreId()
 * @method Ves_Optimize_Model_Crawler setStoreId(int $value)
 * @method int getCategoryId()
 * @method Ves_Optimize_Model_Crawler setCategoryId(int $value)
 * @method int getProductId()
 * @method Ves_Optimize_Model_Crawler setProductId(int $value)
 * @method string getIdPath()
 * @method Ves_Optimize_Model_Crawler setIdPath(string $value)
 * @method string getRequestPath()
 * @method Ves_Optimize_Model_Crawler setRequestPath(string $value)
 * @method string getTargetPath()
 * @method Ves_Optimize_Model_Crawler setTargetPath(string $value)
 * @method int getIsSystem()
 * @method Ves_Optimize_Model_Crawler setIsSystem(int $value)
 * @method string getOptions()
 * @method Ves_Optimize_Model_Crawler setOptions(string $value)
 * @method string getDescription()
 * @method Ves_Optimize_Model_Crawler setDescription(string $value)
 *
 * @category    Ves
 * @package     Ves_Optimize
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ves_Optimize_Model_Crawler extends Mage_Core_Model_Abstract
{
    /**
     * Crawler settings
     */
    const XML_PATH_CRAWLER_ENABLED     = 'system/page_crawl/enable';
    const XML_PATH_CRAWLER_THREADS     = 'system/page_crawl/threads';
    const XML_PATH_CRAWL_MULTICURRENCY = 'system/page_crawl/multicurrency';
    /**
     * Crawler user agent name
     */
    const USER_AGENT = 'MagentoCrawler';

    /**
     * Store visited URLs by crawler
     *
     * @var array
     */
    protected $_visitedUrls = array();

    /**
     * Application instance
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * Adapter factory.
     * Provides http adapter instance
     *
     * @var Ves_Optimize_Model_Adapter_Factory
     */
    protected $_adapterFactory;

    /**
     * Initialize application, adapter factory
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->_app = !empty($args['app']) ? $args['app'] : Mage::app();
        $this->_adapterFactory = !empty($args['adapter_factory']) ? $args['adapter_factory'] :
            Mage::getSingleton('ves_optimize/adapter_factory');
        parent::__construct($args);
    }

    /**
     * Set resource model
     */
    protected function _construct()
    {
        $this->_init('ves_optimize/crawler');
    }

    /**
     * Get internal links from page content
     *
     * @deprecated after 1.11.0.0
     *
     * @param string $pageContent
     * @return array
     */
    public function getUrls($pageContent)
    {
        $urls = array();
        preg_match_all(
            "/\s+href\s*=\s*[\"\']?([^\s\"\']+)[\"\'\s]+/ims",
            $pageContent,
            $urls
        );
        if (isset($urls[1])) {
            $urls = $urls[1];
        }
        return $urls;
    }

    /**
     * Get configuration for stores base urls.
     *
     * array(
     *  $index => array(
     *      'store_id'  => $storeId,
     *      'base_url'  => $url,
     *      'cookie'    => $cookie
     *  )
     * )
     *
     * @return array
     */
    public function getStoresInfo()
    {
        $baseUrls = array();
        foreach ($this->_app->getStores(true) as $store) {
            /** @var $store Mage_Core_Model_Store */
            $website = $this->_app->getWebsite($store->getWebsiteId());
            if ($website->getIsStaging()
                || Mage::helper('ves_websiterestriction')->getIsRestrictionEnabled($store)
            ) {
                continue;
            }
            $baseUrl               = $this->_app->getStore($store)->getBaseUrl();
            $defaultCurrency       = $this->_app->getStore($store)->getDefaultCurrencyCode();
            $defaultWebsiteStore   = $website->getDefaultStore();
            $defaultWebsiteBaseUrl = $defaultWebsiteStore->getBaseUrl();

            $cookie = '';
            if (($baseUrl == $defaultWebsiteBaseUrl) && ($defaultWebsiteStore->getId() != $store->getId())) {
                $cookie = 'store=' . $store->getCode() . ';';
            }

            $baseUrls[] = array(
                'store_id' => $store->getId(),
                'base_url' => $baseUrl,
                'cookie'   => $cookie,
            );
            if ($store->getConfig(self::XML_PATH_CRAWL_MULTICURRENCY)
                && $store->getConfig(Ves_Optimize_Model_Processor::XML_PATH_CACHE_MULTICURRENCY)) {
                $currencies = $store->getAvailableCurrencyCodes(true);
                foreach ($currencies as $currencyCode) {
                    if ($currencyCode != $defaultCurrency) {
                        $baseUrls[] = array(
                            'store_id' => $store->getId(),
                            'base_url' => $baseUrl,
                            'cookie'   => $cookie . 'currency=' . $currencyCode . ';'
                        );
                    }
                }
            }
        }
        return $baseUrls;
    }

    /**
     * Crawl all system urls
     *
     * @return Ves_Optimize_Model_Crawler
     */
    public function crawl()
    {
        if (!$this->_app->useCache('full_page')) {
            return $this;
        }

        $adapter = $this->_adapterFactory->getHttpCurlAdapter();

        foreach ($this->getStoresInfo() as $storeInfo) {
            $this->_visitedUrls = array();
            if (!$this->_isCrawlerEnabled($storeInfo['store_id'])) {
                continue;
            }

            $this->_executeRequests($storeInfo, $adapter);
        }
        return $this;
    }

    /**
     * Prepares and executes requests by given request_paths values
     *
     * @param array $info
     * @param Varien_Http_Adapter_Curl $adapter
     */
    protected function _executeRequests(array $info, Varien_Http_Adapter_Curl $adapter)
    {
        $storeId = $info['store_id'];
        $options = array(CURLOPT_USERAGENT => self::USER_AGENT);
        $threads = $this->_getCrawlerThreads($storeId);
        if (!$threads) {
            $threads = 1;
        }
        if (!empty($info['cookie'])) {
            $options[CURLOPT_COOKIE] = $info['cookie'];
        }
        $urls = array();
        $urlsCount = $totalCount = 0;

        foreach ($this->_getResource()->getRequestPaths($storeId) as $requestPath) {
            $url = $info['base_url'] . $requestPath;
            $urlHash = md5($url);
            if (isset($this->_visitedUrls[$urlHash])) {
                continue;
            }
            $urls[] = $url;
            $this->_visitedUrls[$urlHash] = true;
            $urlsCount++;
            $totalCount++;
            if ($urlsCount == $threads) {
                $adapter->multiRequest($urls, $options);
                $urlsCount = 0;
                $urls = array();
            }
        }
        if (!empty($urls)) {
            $adapter->multiRequest($urls, $options);
        }
    }

    /**
     * Retrieves number of crawler threads
     *
     * @param int $storeId
     * @return int
     */
    protected function _getCrawlerThreads($storeId)
    {
        return (int)$this->_app->getStore($storeId)->getConfig(self::XML_PATH_CRAWLER_THREADS);
    }

    /**
     * Checks whether crawler is enabled for given store
     *
     * @param int $storeId
     * @return null|string
     */
    protected function _isCrawlerEnabled($storeId)
    {
        return (bool)(string)$this->_app->getStore($storeId)->getConfig(self::XML_PATH_CRAWLER_ENABLED);
    }
}

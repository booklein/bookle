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
 * Catalog product view processor
 */
class Ves_Optimize_Model_Processor_Product extends Ves_Optimize_Model_Processor_Default
{
    /**
     * Key for saving product id in metadata
     */
    const METADATA_PRODUCT_ID = 'current_product_id';

    /**
     * Prepare response body before caching
     *
     * @param Zend_Controller_Response_Http $response
     * @return string
     */
    public function prepareContent(Zend_Controller_Response_Http $response)
    {
        $cacheInstance = Ves_Optimize_Model_Cache::getCacheInstance();

        /** @var Ves_Optimize_Model_Processor */
        $processor = Mage::getSingleton('ves_optimize/processor');
        $countLimit = Mage::getStoreConfig(Mage_Reports_Block_Product_Viewed::XML_PATH_RECENTLY_VIEWED_COUNT);
        // save recently viewed product count limit
        $cacheId = $processor->getRecentlyViewedCountCacheId();
        if (!$cacheInstance->getFrontend()->test($cacheId)) {
            $cacheInstance->save($countLimit, $cacheId, array(Ves_Optimize_Model_Processor::CACHE_TAG));
        }
        // save current product id
        $product = Mage::registry('current_product');
        if ($product) {
            $cacheId = $processor->getRequestCacheId() . '_current_product_id';
            $cacheInstance->save($product->getId(), $cacheId, array(Ves_Optimize_Model_Processor::CACHE_TAG));
            $processor->setMetadata(self::METADATA_PRODUCT_ID, $product->getId());
            Ves_Optimize_Model_Cookie::registerViewedProducts($product->getId(), $countLimit);
        }

        return parent::prepareContent($response);
    }
}

<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Blog Extension
 *
 * @category   Ves
 * @package    Ves_Blog
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Blog_Block_List extends Mage_Core_Block_Template
{
	/**
	 * @var string $_config
	 *
	 * @access protected
	 */
	protected $_config = '';

	/**
	 * @var string $_config
	 *
	 * @access protected
	 */
	protected $_listDesc = array();

	/**
	 * @var string $_config
	 *
	 * @access protected
	 */
	protected $_show = 0;
	protected $_theme = "";

	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{

		$this->convertAttributesToConfig($attributes);

		parent::__construct();

		if(!$this->getGeneralConfig("show")) {
			return;
		}
		/*Cache Block*/
		$cache_type = Ves_Blog_Model_Config::CACHE_BLOCK_LATEST_TAG;
		$block_type = $this->getConfig("block_type", "module_setting", "latest");

		switch ($block_type) {
			case 'widget_latest':
			$cache_type = Ves_Blog_Model_Config::CACHE_WIDGET_LATEST_TAG;
			break;
			case 'latest':
			default:
			$cache_type = Ves_Blog_Model_Config::CACHE_BLOCK_LATEST_TAG;
			break;
		}

		$enable_cache = $this->getConfig("enable_cache", 0 );
		if(!$enable_cache) {
			$cache_lifetime = null;
		} else {
			$cache_lifetime = $this->getConfig("cache_lifetime", 86400 );
			$cache_lifetime = (int)$cache_lifetime>0?$cache_lifetime: 86400;
		}

		$this->addData(array('cache_lifetime' => $cache_lifetime));

		$this->addCacheTag(array(
			Mage_Core_Model_Store::CACHE_TAG,
			Mage_Cms_Model_Block::CACHE_TAG,
			$cache_type
			));

		/*End Cache Block*/
	}

	/**
     * Get Key pieces for caching block content
     *
     * @return array
     */
	public function getCacheKeyInfo()
	{
		$block_cache_type  = 'VES_BLOG_BLOCK_LATEST';
		$block_type = $this->getConfig("block_type", "module_setting", "latest");
		switch ($block_type) {
			case 'widget_latest':
			$block_cache_type = 'VES_BLOG_WIDGET_LATEST';
			break;
			case 'latest':
			default:
			$block_cache_type = 'VES_BLOG_BLOCK_LATEST';
			break;
		}

		return array(
			$block_cache_type,
			$this->getNameInLayout(),
			Mage::app()->getStore()->getId(),
			Mage::getDesign()->getPackageName(),
			Mage::getDesign()->getTheme('template'),
			Mage::getSingleton('customer/session')->getCustomerGroupId(),
			'template' => $this->getTemplate(),
			);
	}

	public function convertAttributesToConfig($attributes = array()) {
		if($attributes) {
			foreach($attributes as $key=>$val) {
				$this->setConfig($key, $val);
			}
		}
	}
	public function getGeneralConfig( $val ){
		return Mage::getStoreConfig( "ves_blog/general_setting/".$val );
	}

	/**
	 * get value of the extension's configuration
	 *
	 * @return string
	 */
	public function getConfig( $key, $panel='module_setting', $default = ""){
		$return = "";
		$value = $this->getData($key);
	    //Check if has widget config data
		if($this->hasData($key) && $value !== null) {
			if($key == "latestmod_desc") {
				$value = base64_decode($value);
			}
			if($value == "true") {
				return 1;
			} elseif($value == "false") {
				return 0;
			}

			return $value;

		} else {

			if(isset($this->_config[$key])){
				$return = $this->_config[$key];
			}else{
				$return = Mage::getStoreConfig("ves_blog/$panel/$key");
			}
			if($return == "" && !$default) {
				$return = $default;
			}

		}

		return $return;
	}
	/**
     * overrde the value of the extension's configuration
     *
     * @return string
     */
	function setConfig($key, $value) {
		if($value == "true") {
			$value =  1;
		} elseif($value == "false") {
			$value = 0;
		}
		if($value != "") {
			$this->_config[$key] = $value;
		}

		return $this;
	}
}
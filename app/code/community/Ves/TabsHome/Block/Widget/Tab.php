<?php
class Ves_TabsHome_Block_Widget_Tab extends Ves_TabsHome_Block_List implements Mage_Widget_Block_Interface
{
	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{
	 	parent::__construct($attributes);

		if (!$this->getData('load_ajax')) {
	        /*Cache Block*/

	          $enable_cache = $this->getConfig("enable_cache", 0 );
	          if(!$enable_cache) {
	            $cache_lifetime = null;
	          } else {
	            $cache_lifetime = $this->getConfig("cache_lifetime", 86400 );
	            $cache_lifetime = (int)$cache_lifetime>0?$cache_lifetime: 86400;
	          }

	          $this->addData(array('cache_lifetime' => $cache_lifetime));

	          $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
	          $this->addCacheTag(array(
	            Mage_Core_Model_Store::CACHE_TAG,
	            Mage_Cms_Model_Block::CACHE_TAG,
	            Ves_TabsHome_Model_Product::CACHE_WIDGET_TAG,
	            $currency_code
	          ));

	        /*End Cache Block*/
	    }
	}

	public function _toHtml() {
        return parent::_toHtml();
	}

	/**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
    	$currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        return array(
           'VES_TABSHOME_BLOCK_WIDGET_TAB',
           $this->getNameInLayout(),
           Mage::app()->getStore()->getId(),
           Mage::getDesign()->getPackageName(),
           Mage::getDesign()->getTheme('template'),
           Mage::getSingleton('customer/session')->getCustomerGroupId(),
           'template' => $this->getTemplate(),
           $currency_code
        );
    }
}
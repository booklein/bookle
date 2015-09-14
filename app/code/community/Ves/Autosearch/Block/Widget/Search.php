<?php
class Ves_Autosearch_Block_Widget_Search extends Ves_Autosearch_Block_Autosearch implements Mage_Widget_Block_Interface
{

	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{
	 	parent::__construct($attributes);
		/*Cache Block*/

		//Convert widget config
		$thumb_height = $this->getConfig("thumb_height");
		if($thumb_height) {
			$this->setConfig("thumbHeight", $thumb_height);
		}
		
		$thumb_width = $this->getConfig("thumb_width");
		if($thumb_width) {
			$this->setConfig("thumbWidth", $thumb_width);
		}
		

        $enable_cache = $this->getConfig("enable_cache", 1 );
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
          Ves_Autosearch_Model_Search::CACHE_WIDGET_TAG
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
        return array(
           'VES_AUTOSEARCH_WIDGET_SEARCH',
           $this->getNameInLayout(),
           Mage::app()->getStore()->getId(),
           Mage::getDesign()->getPackageName(),
           Mage::getDesign()->getTheme('template'),
           Mage::getSingleton('customer/session')->getCustomerGroupId(),
           'template' => $this->getTemplate(),
        );
    }


	public function _toHtml() {
        return parent::_toHtml();
	}

}
<?php
class Ves_ProductCarousel2_Block_Widget_Carousel extends Ves_ProductCarousel2_Block_List implements Mage_Widget_Block_Interface
{

	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{
	 	parent::__construct($attributes);
		/*Cache Block*/

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
	      	Ves_ProductCarousel2_Model_Config::CACHE_WIDGET_TAG
    	));

    	/*End Cache Block*/
	}

	public function _toHtml() {
		$this->_show = $this->getConfig("show");
		
		if(!$this->_show) return;
		//Override Config
		if(isset($this->_config) && $this->_config && is_array( $this->_config)) {
			foreach($this->_config as $key=>$val) {
				if($this->hasData($key)) {
					$this->setConfig($key, $this->getData($key));
				}
			}
		}
		//Convert widget config
		$mode = $this->getData("source_products_mode");
		if($mode) {
			$this->setConfig("sourceProductsMode", $mode);
		}
		
		$thumbnail_mode = $this->getData("thumbnail_mode");
		if($thumbnail_mode) {
			$this->setConfig("thumbnailMode", $thumbnail_mode);
		}
		$thumb_height = $this->getData("thumb_height");
		if($thumb_height) {
			$this->setConfig("thumbHeight", $thumb_height);
		}
		$thumb_width = $this->getData("thumb_width");
		if($thumb_width) {
			$this->setConfig("thumbWidth", $thumb_width);
		}

		$title_maxchar = $this->getData("title_maxchar");
		if($title_maxchar) {
			$this->setConfig("titleMaxchar", $title_maxchar);
		}

		$desc_maxchar = $this->getData("desc_maxchar");
		if($desc_maxchar) {
			$this->setConfig("descMaxchar", $desc_maxchar);
		}

        return parent::_toHtml();
	}

	/**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
           'VES_PRODUCTCAROUSEL2_BLOCK_WIDGET_TAB',
           $this->getNameInLayout(),
           Mage::app()->getStore()->getId(),
           Mage::getDesign()->getPackageName(),
           Mage::getDesign()->getTheme('template'),
           Mage::getSingleton('customer/session')->getCustomerGroupId(),
           'template' => $this->getTemplate(),
        );
    }

}
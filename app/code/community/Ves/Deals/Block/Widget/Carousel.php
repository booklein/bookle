<?php
class Ves_Deals_Block_Widget_Carousel extends Ves_Deals_Block_Carousel implements Mage_Widget_Block_Interface
{
	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{
	 	parent::__construct($attributes);

		$this->addCacheTag(array(
      Mage_Core_Model_Store::CACHE_TAG,
      Mage_Cms_Model_Block::CACHE_TAG,
      Ves_Deals_Model_Config::CACHE_WIDGET_TAG
    ));
	}
  
  /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
           'VES_DEALS_WIDGET_LIST',
           $this->getNameInLayout(),
           Mage::app()->getStore()->getId(),
           Mage::getDesign()->getPackageName(),
           Mage::getDesign()->getTheme('template'),
           Mage::getSingleton('customer/session')->getCustomerGroupId(),
           'template' => $this->getTemplate(),
        );
    }

	public function _toHtml() {
		$this->_show = $this->getConfig("show");
		
		if(!$this->_show) return;
		if(isset($this->_config) && $this->_config) {
			foreach($this->_config as $key=>$val) {
				if($this->hasData($key)) {
					$this->setConfig($key, $this->getData($key));
				}
			}
		}

		//Convert widget config
		$source_products_mode = $this->getData("source_products_mode");
		if($source_products_mode) {
			$this->setConfig("sourceProductsMode", $source_products_mode);
		}
		
		$title_maxchar = $this->getData("title_maxchar");
		if($title_maxchar) {
			$this->setConfig("titleMaxchar", $title_maxchar);
		}
		$desc_maxchar = $this->getData("desc_maxchar");
		if($desc_maxchar) {
			$this->setConfig("descMaxchar", $desc_maxchar);
		}

		/*Copy code from construct function of parent class*/
		/*
		if($this->hasData("template")) {
			$this->setTemplate( $this->getData("template") );
		} else {
			$template = 'ves/deals/block/carousel.phtml';
			$this->setTemplate( $template );
		}
		*/
        return parent::_toHtml();
	}
}
<?php 
/*------------------------------------------------------------------------
 # VenusTheme Brand Module 
 # ------------------------------------------------------------------------
 # author:    VenusTheme.Com
 # copyright: Copyright (C) 2012 http://www.venustheme.com. All Rights Reserved.
 # @license: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Websites: http://www.venustheme.com
 # Technical Support:  http://www.venustheme.com/
-------------------------------------------------------------------------*/
class Ves_Brand_Block_Scroll extends Ves_Brand_Block_List 
{
	protected $_config = array();
	var $_show = true;

	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{

  		parent::__construct($attributes);

  		 /*End Cache Block*/
        
  		$template = "";

  		if($this->hasData("template") && $this->getData("template")) {
        	$template = $this->getData("template");
        } elseif(isset($attributes['template']) && $attributes['template']) {
        	$template = $attributes['template'];
        } else{
 			$template = "ves/brand/block/scroll.phtml";
 		}

      	$this->setTemplate($template);
			
		/*Cache Block*/
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
          Ves_Brand_Model_Brand::CACHE_BLOCK_SCROLL_TAG
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
           'VES_BRAND_BLOCK_SCROLL',
           $this->getNameInLayout(),
           Mage::app()->getStore()->getId(),
           Mage::getDesign()->getPackageName(),
           Mage::getDesign()->getTheme('template'),
           Mage::getSingleton('customer/session')->getCustomerGroupId(),
           'template' => $this->getTemplate(),
        );
    }
    /**
     * overrde the value of the extension's configuration
     *
     * @return string
     */
    function setConfig($key, $value) {	
    	$this->_config[$key] = $value;
    	return $this;
    }

	public function getGeneralConfig( $val, $default = "" ){ 

		return Mage::getStoreConfig( "ves_brand/general_setting/".$val );
	}

	public function getModuleConfig( $val, $default = "" ){
		$return = "";
	    $value = $this->getData($val);
	    //Check if has widget config data
	    if($this->hasData($val) && $value !== null) {

	      if($value == "true") {
	        return 1;
	      } elseif($value == "false") {
	        return 0;
	      }
	      return $value;
	      
	    } else {

	      if(isset($this->_config[$val])){
	        $return = $this->_config[$val];
	      }else{
	        $return = Mage::getStoreConfig("ves_brand/module_setting/".$val );
	      }
	      if($return == "" && $default) {
	        $return = $default;
	      }

	    }

	    return $return;
	}
	public function _toHtml(){
		$this->_show = $this->getGeneralConfig("show");
		$enable_scroll = $this->getModuleConfig("enable_scrollmodule");
		$limit = (int)$this->getConfig('itemvisiable');
		if(!$this->_show || !$enable_scroll) return;

		$collection = Mage::getModel( 'ves_brand/brand' )
						->getCollection();

		$collection ->addFieldToFilter('is_active', 1);
		$collection ->setOrder( 'position', 'ASC' );
		if($limit){
			$collection ->setPageSize($limit);
		}
		$resroute = Mage::getStoreConfig('ves_brand/general_setting/route');
		$extension = ".html";
		foreach( $collection as $model ){
			if(!$model->getLink()){
				Mage::getModel('core/url_rewrite')->loadByIdPath('venusbrand/brand/'.$model->getId())
							->setIdPath('venusbrand/brand/'.$model->getId())
							->setRequestPath($resroute .'/'.$model->getIdentifier().$extension  )
							->setTargetPath('venusbrand/brand/view/id/'.$model->getId())
							->save();
			}	
		}

		$this->assign( 'brands', $collection );
		  
		return parent::_toHtml();
		
	}

}	
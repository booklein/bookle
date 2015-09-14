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

class Ves_Brand_Block_Brand_List extends Mage_Catalog_Block_Product_List {
  
  var $_show = true;
  /**
   * Contructor
   */
  public function __construct($attributes = array())
  {
    $this->_show = $this->getGeneralConfig("show");
    
    if(!$this->_show) return;

    parent::__construct( $attributes );

    $config_template = $this->getGeneralConfig("listing_layout");

    $my_template = "";
    if(isset($attributes['template']) && $attributes['template']) {

      $my_template = $attributes['template'];

    } elseif($this->hasData("template")) {
      $my_template = $this->getData("template");

    } elseif($config_template) {

      $my_template = "ves/brand/{$config_template}.phtml";

    } else {

      $my_template = "ves/brand/list.phtml";

    }

    $this->setTemplate($my_template);

  }

  public function getGeneralConfig( $val ){ 
    return Mage::getStoreConfig( "ves_brand/general_setting/".$val );
  }
  
  public function getConfig( $val ){ 
    return Mage::getStoreConfig( "ves_brand/module_setting/".$val );
  }
  
    protected function _prepareLayout()
    {
        $title = $this->getConfig("brandnav_title");
        if(!$title) {
          $title =  $this->__("All Brands");
        }
        $this->getLayout()->getBlock('head')->setTitle($title);

        $this->getCountingPost();

        return parent::_prepareLayout();
    }
  
  public function getBrands(){
    $page = $this->getRequest()->getParam('page') ? $this->getRequest()->getParam('page') : 1;
    $page = (($page - 1) > 0)?($page-1):0;
    $limit = (int)$this->getGeneralConfig("list_limit");
    $keyword = $this->getRequest()->getParam( "search_query" );
    $keyword = trim($keyword);

    $collection = Mage::getModel('ves_brand/brand')->getCollection();

    if($keyword && strlen($keyword) >= 3) {
       $collection->addKeywordFilter($keyword);
    }

    $collection->getSelect()->limit($limit, $page*$limit);

    return $collection;
  }

  public function getCountingPost(){
    $limit = (int)$this->getGeneralConfig("list_limit");
    $keyword = $this->getRequest()->getParam( "search_query" );
    $keyword = trim($keyword);

    $collection = Mage::getModel('ves_brand/brand')->getCollection();

    if($keyword && strlen($keyword) >= 3) {
        $collection->addKeywordFilter($keyword);
    }
    

    Mage::register( 'paginateTotal', count($collection) );
    Mage::register( "paginateLimitPerPage", $limit );
  }

 
  public function getBrand() {
      return Mage::registry('current_brand');
  }

}
?>
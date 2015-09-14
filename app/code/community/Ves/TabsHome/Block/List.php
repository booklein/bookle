<?php

class Ves_TabsHome_Block_List extends Mage_Catalog_Block_Product_Abstract {

	protected $_config = '';
	protected $_categories;

  protected $_attributes = array();

  public function __construct($attributes = array()) {

    $this->_attributes = $attributes;
    $helper = Mage::helper('ves_tabshome/data');

    foreach ($attributes as $k => $v) {
      if( $v == 'false' ){
        $attributes[$k] = false;
      }
      if( $v == 'true' ){
        $attributes[$k] = true;
      }
    }

    $this->_config = $helper->get($attributes);

    if( !$this->getConfig("show") ){ return ;}

    /* End init meida files */
    $mediaHelper = Mage::helper('ves_tabshome/media');
    $config = $this->_config;
    $this->_config['list_cat'] = (empty($config['list_cat']) ? '': $config['list_cat']);

  		//if( !isset($this->_config["show"])  || (isset($this->_config["show"]) && !$this->_config["show"] )) { return ; }

    parent::__construct();

    if($this->getConfig("template")) {

      $config['template'] = $this->getConfig("template");

    } elseif (!$this->hasData("template") && $this->getConfig('load_ajax')) {

     $config['template'] = 'ves/tabshome/default/ajax.phtml';

   }elseif(!$this->hasData("template") && !$this->getConfig('load_ajax')){

     $config['template'] = 'ves/tabshome/default/default.phtml';

   }elseif($this->hasData("template")){

    $config['template'] = $this->getData("template");

  }

  $this->setTemplate($config['template']);

  if (!$this->getConfig('load_ajax')) {
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
      Ves_TabsHome_Model_Product::CACHE_BLOCK_TAG,
      $currency_code
      ));

    /*End Cache Block*/
  }

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
       'VES_TABSHOME_BLOCK_LIST',
       $this->getNameInLayout(),
       Mage::app()->getStore()->getId(),
       Mage::getDesign()->getPackageName(),
       Mage::getDesign()->getTheme('template'),
       Mage::getSingleton('customer/session')->getCustomerGroupId(),
       'template' => $this->getTemplate(),
       $currency_code
       );
    }

    public function convertAttributesToConfig($attributes = array()) {
      if($attributes) {
        foreach($attributes as $key=>$val) {
          $this->setConfig($key, $val);
        }
      }
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

    /**
     * get value of the extension's configuration
     *
     * @return string
     */
    public function getConfig( $key, $default = "", $panel='ves_tabshome'){
      $return = "";
      $value = $this->getData($key);
      //Check if has widget config data
      if($this->hasData($key) && $value !== null) {
        if($key == "pretext") {
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

          if($return == "true") {
            $return = 1;
          } elseif($return == "false") {
            $return = 0;
          }

        }else{
          $return = Mage::getStoreConfig("ves_tabshome/$panel/$key");
        }
        if($return == "" && $default) {
          $return = $default;
        }

      }

      return $return;
    }

    /**
     * Rendering block content
     *
     * @return string
     */
    public function _toHtml() {

      $config = $this->_config;

      if(isset($config['ajax_request']) && $config['ajax_request'] && $this->getConfig("load_ajax")) {
        $this->setTemplate('ves/tabshome/default/ajax.phtml'); 
      }

      $currency = ''.Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();

      $cms = "";

      $cms_block_id = $this->getConfig('cmsblock');
        //Get pretext from cms block
      if($cms_block_id){
        $cms = Mage::getSingleton('core/layout')->createBlock('cms/block')->setBlockId($cms_block_id)->toHtml();
      }

      $news = $allproducts = $featured = $specical = $bestseller = $mostview = $related = $upsell = $toprated = array();

      if($this->getConfig('load_ajax') && $this->getConfig('enable_allproducts')){
        $allproducts = $this->getProducts('allproducts');
        $this->assign( 'allproducts', $allproducts['products'] );
      }

      if($this->getConfig('enable_new')){
        $news = $this->getProducts('news');
        $this->assign( 'news', $news['products'] );
      }

      if($this->getConfig('enable_feature')){
        $featured = $this->getProducts('featured');
        $this->assign( 'featured', $featured['products'] );
      }

      if($this->getConfig('enable_bestseller')){
        $bestseller = $this->getProducts('bestseller');
        $this->assign( 'bestseller', $bestseller['products'] );
      }

      if($this->getConfig('enable_mostview')){
        $mostview = $this->getProducts('mostview');
        $this->assign( 'mostview', $mostview['products'] );
      }

      if($this->getConfig('enable_special')){
        $specical = $this->getProducts('specical');
        $this->assign( 'specical', $specical['products'] );
      }

      if( $this->getConfig('enable_toprated', 0) ){
        $toprated = $this->getProducts('toprated');
        $this->assign( 'toprated', $toprated['products'] );
      }

      if($this->getConfig('enable_related') && Mage::registry('current_product')){
        $related = $this->getProducts('related');
        $this->assign( 'related', $related['products'] );
      }

      if($this->getConfig('enable_upsell') && Mage::registry('current_product')){
        $upsell = $this->getProducts('upsell');
        $this->assign( 'upsell', $upsell['products'] );
      }
      $attributes = serialize($this->_attributes);
      $this->assign( 'attributes', base64_encode($attributes));
      $this->assign( 'cms', $cms );
      $this->assign( 'tabs' , $this->getTabs() );
      $this->assign( 'source_type' , $this->getConfig('source_type',0) );
      $this->assign( 'ajax_request', $this->getConfig('ajax_request',0) );
      $this->assign('currency', $currency);
      $this->assign('config', $config);
      return parent::_toHtml();
    }


    public function getPro()
    {
      $storeId    = Mage::app()->getStore()->getId();
      $products = Mage::getResourceModel('reports/product_collection')
                      ->addAttributeToSelect('*')
                      ->addAttributeToSelect(array('name', 'price', 'small_image')) //edit to suit tastes
                      ->setStoreId($storeId)
                      ->addStoreFilter($storeId);

                      Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
                      Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);

                      $products->setPageSize(6)->setCurPage(1);

                      $this->setProductCollection($products);
    }

    function inArray($source, $target) {
       for($i = 0; $i < sizeof ( $source ); $i ++) {
        if (in_array ( $source [$i], $target )) {
         return true;
       }
     }
    }

    public function getProducts($source_type){
       $config = $this->_config;

       if(!$this->getConfig('load_ajax') ){
        $config['itemspage'] = $config['limit_item'];
      }                   

      $list = array();
      $data = array();
      switch ($source_type) {
        case 'allproducts':
        $data = Mage::getModel('ves_tabshome/product')->getListAllProducts( $config );
        break;
        case 'news':
        $data = Mage::getModel('ves_tabshome/product')->getListNewProducts( $config );
        break;
        case 'featured':
        $data = Mage::getModel('ves_tabshome/product')->getListFeaturedProducts( $config );
        break;
        case 'bestseller':
        $data = Mage::getModel('ves_tabshome/product')->getListBestSellerProducts( $config );
        break;
        case 'specical':
        $data = Mage::getModel('ves_tabshome/product')->getListMostViewedProducts( $config );
        break;
        case 'mostview':
        $data = Mage::getModel('ves_tabshome/product')->getListSpecialProducts( $config );
        break;
        case 'toprated':
        $data = Mage::getModel('ves_tabshome/product')->getListTopRatedProducts( $config );
        break;
        case 'related':
        $data = Mage::getModel('ves_tabshome/product')->getListRelatedProducts( $config );
        break;	
        case 'upsell':
        $data = Mage::getModel('ves_tabshome/product')->getListUpsellProducts( $config );
        break; 
      }

      if(!$data['products'] || $data['products']==''){ return array(); }
        $list['products'] = $data['products'];
        $list['hasNextData'] = $data['hasNextData'];
      return $list;
  }

  public function subString( $text, $length = 100, $replacer ='...', $is_striped=true ){
      $text = ($is_striped==true)?strip_tags($text):$text;
       if(strlen($text) <= $length){
        return $text;
      }
      $text = substr($text,0,$length);
      $pos_space = strrpos($text,' ');
      return substr($text,0,$pos_space).$replacer;
  }

  public function getTabs(){

      $tabs = array();

      if($this->getConfig('enable_allproducts', 0)){
        $tabs['allproducts'] = Mage::helper('ves_tabshome')->__('All Products');
      }

      if($this->getConfig('enable_new', 0)){
        $tabs['news'] = Mage::helper('ves_tabshome')->__('New Arrivals');
      }
      if($this->getConfig('enable_feature', 0)){
        $tabs['featured'] = Mage::helper('ves_tabshome')->__('Featured');
      }
      if($this->getConfig('enable_bestseller', 0)){
        $tabs['bestseller'] = Mage::helper('ves_tabshome')->__('Best Seller');
      }
      if($this->getConfig('enable_special', 0)){
        $tabs['specical'] = Mage::helper('ves_tabshome')->__('Special');
      }
      if($this->getConfig('enable_mostview', 0)){
        $tabs['mostview'] = Mage::helper('ves_tabshome')->__('Most Viewed');
      }
      if($this->getConfig('enable_toprated', 0)){
        $tabs['toprated'] = Mage::helper('ves_tabshome')->__('Top Rated');
      }
      if($this->getConfig('enable_related', 0) && Mage::registry('current_product')){
        $tabs['related'] = Mage::helper('ves_tabshome')->__('Related');
      }
      if($this->getConfig('enable_upsell') && Mage::registry('current_product')){
        $tabs['upsell'] = Mage::helper('ves_tabshome')->__('Up Sell');
      }
      $tabs_position = array();
      $tabs_position['allproducts'] = array("tab"=>"allproducts", "position" => (int)$this->getConfig("tab_allproducts", 1, "order_tabs"));
      $tabs_position['news'] = array("tab"=>"news", "position" => (int)$this->getConfig("tab_news", 2, "order_tabs"));
      $tabs_position['featured'] = array("tab"=>"featured", "position" => (int)$this->getConfig("tab_featured", 3, "order_tabs"));
      $tabs_position['bestseller'] = array("tab"=>"bestseller", "position" => (int)$this->getConfig("tab_bestseller", 4, "order_tabs"));
      $tabs_position['specical'] = array("tab"=>"specical", "position" => (int)$this->getConfig("tab_special", 5, "order_tabs"));
      $tabs_position['mostview'] = array("tab"=>"mostview", "position" => (int)$this->getConfig("tab_mostview", 6, "order_tabs"));
      $tabs_position['toprated'] = array("tab"=>"toprated", "position" => (int)$this->getConfig("tab_toprated", 7, "order_tabs"));
      $tabs_position['related'] = array("tab"=>"related", "position" => (int)$this->getConfig("tab_related", 8, "order_tabs"));
      $tabs_position['upsell'] = array("tab"=>"upsell", "position" => (int)$this->getConfig("tab_upsell", 9, "order_tabs"));

      usort($tabs_position, function($a, $b) {
        return $a['position'] - $b['position'];
      });
      
      if($tabs_position && $tabs) {
        $tmp = array();
        foreach($tabs_position as $key=>$item) {
          if(isset($tabs[$item['tab']])) {
            $tmp[$item['tab']] = $tabs[$item['tab']];
          }
        }
        if($tmp)
          $tabs = $tmp;
      }

      return $tabs;
  }

}

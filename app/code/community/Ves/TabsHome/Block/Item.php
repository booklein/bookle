<?php

class Ves_TabsHome_Block_Item extends Mage_Catalog_Block_Product_Abstract {

  protected $_config = '';

  public function __construct($attributes=array()) {
    $helper = Mage::helper('ves_tabshome/data');

    $this->_config = $helper->get($attributes);

    /* End init meida files */
    $mediaHelper = Mage::helper('ves_tabshome/media');
    $config = $this->_config;

    $this->setTemplate('ves/tabshome/default/item.phtml');
    parent::__construct();
  }

  public function _toHtml(){
    $config = $this->_config;
    $this->assign('config', $config);
    return parent::_toHtml();
  }

  public function setAllConfig($config){
    $this->_config = $config;
    return $this;
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
}
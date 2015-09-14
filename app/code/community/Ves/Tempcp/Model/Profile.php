<?php
/**
 * Tempcp for Magento
 *
 * @category   Ves
 * @package    Ves_Tempcp
 * @copyright  Copyright (c) 2009 Ves GmbH & Co. KG <magento@Ves.de>
 */

/**
 * Tempcp for Magento
 *
 * @category   Ves
 * @package    Ves_Tempcp
 * @author     Landofcoder <landofcoder@gmail.com>
 */
class Ves_Tempcp_Model_Profile extends Varien_Object
{
   var $_ini_file_path = "";
   var $_theme_path = "";
   
   var $_theme_name = "";

   var $_params = array();

   public function __constract($theme_name = "") {
        $this->getInstance($theme_name);
   }
   public function getInstance($theme_name = "", $default_profile = "") {
        if($theme_name) {
            $this->_theme_path        = Mage::getBaseDir('skin') . '/frontend/'.$theme_name;
            $this->_ini_file_path = $this->_theme_path.'/etc/config.ini';
            if($default_profile) {
                $this->_ini_file_path = $this->_theme_path.'/etc/'.$default_profile.'/config.ini';
            }
            
            $this->_params = array();
            $this->getThemeProfile();
        }
        return $this;
   }
   public function getThemeProfile($ini_file_path = "") {
        $config_ini = $this->_ini_file_path;
        if($ini_file_path) {
            $config_ini = $ini_file_path;
        }
        $params = array();
        /*get default theme option*/
        if(file_exists($config_ini)){
            $data_ini = file_get_contents($config_ini);
            $tmp = explode("\n", $data_ini);
            if($tmp){
                foreach($tmp as $item){
                    if($item){
                        $tmp2 = explode("=",$item);
                        $key = isset($tmp2[0])?trim($tmp2[0]):"";
                        $val = isset($tmp2[1])?trim($tmp2[1]):"";

                        if($key){
                            $params[$key] = $val;
                        }
                    }
                    
                }
            }
        }
        $this->setParams($params);
        return $this;
   }

   public function setParams($array = array()){
      $this->_params = $array;
   }

   public function setParam($key, $value = ""){
        if(!is_array($this->_params)) {
            $this->_params = array();
        }
        $this->_params[ $key ] = $value;
   }
   public function getParam($key, $default = ""){
        return isset($this->_params[$key])?$this->_params[$key]:$default;
   }
}

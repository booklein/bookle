<?php
/******************************************************
 * @package Ves Magento Theme Framework for Magento 1.4.x or latest
 * @version 1.1
 * @author http://www.venusthemes.com
 * @copyright	Copyright (C) Feb 2013 VenusThemes.com <@emai:venusthemes@gmail.com>.All rights reserved.
 * @license		GNU General Public License version 2
*******************************************************/
if(!defined("VES_TEMCP_HELPER_PATH")) {
	define("VES_TEMCP_HELPER_PATH", Mage::getBaseDir('code')."/community/Ves/Tempcp/Helper/");
}

require_once (VES_TEMCP_HELPER_PATH."field.php");

class Tempcp_Theme_Core extends Mage_Core_Helper_Abstract{
	protected $error = array(); 
	protected $theme = '';
	protected $default_profile = '';
	protected $theme_path = '';
	protected $params = "";
	protected $element_group = "themecontrol";
	protected $config_ini = null;
	public $store_id = 0;
	public $theme_name = '';
	public $positions = array();
	public $default_config = array();
	public $skins = array();
	public $modules = false;
	public $custom_settings = array();

	/**
	 * Get all config params of theme
	 * @param: $property is string, that field name of theme config which you want get
	 * @param: $default is string, when field is not exists function will return default
	 * @param: $run_mage_variables is boolean, allow run helper to convert magento variables to html
	 * @return: string
	 */
	public function get($property, $default = "", $run_mage_variables = false) {
		$default = (empty($default) && isset($this->default_config[$property]))?$this->default_config[$property]:$default;
		$profile = $this->getProfile();
		$return = "";
		if(isset($this->$property)){
			$return = $this->$property;
		}elseif(isset($this->params[$property])){
			$return = $this->params[$property];
		}else{
			if(is_object($profile) && $tmp_default = $profile->getParam($property, "")) {
				$default = $tmp_default;
			}
			$return = $default;
		}
		if($run_mage_variables){
			$processor = Mage::helper('cms')->getPageTemplateProcessor();
			$return = $processor->filter($return);
		}
        return $return;
    }
    /**
	 *
	 */
    public function set($property, $value) {
        $this->$property = $value;
        return $this;
    }
    
	public function isVenusTheme($theme_name = "") {
		$show_tempcp = Mage::getStoreConfig("ves_tempcp/ves_tempcp/show");
		if($show_tempcp) {
			if(!$theme_name) {
	    		$theme_name =  Mage::getDesign()->getTheme('frontend');
	    		$package = Mage::getSingleton('core/design_package')->getPackageName();
	    		$theme_name = $package."/".$theme_name;
	    	}
			if(!($_model = Mage::registry('ves_theme_model'))) {
				$_model  = Mage::getModel('ves_tempcp/theme')->loadThemeByGroup($theme_name);
				Mage::unregister('ves_theme_model');
				Mage::register('ves_theme_model', $_model);
			}
			if($_model && $_model->getId()){
				return true;
			}
		}
		
		return false;
	}

    public function getCurrentTheme($theme_name = "", $theme_id = 0, $check_exists = false){
    	$_isset_theme_setting = false;
    	if(!$theme_name) {
    		$theme_name =  Mage::getDesign()->getTheme('frontend');
    		$package = Mage::getSingleton('core/design_package')->getPackageName();
    		$theme_name = $package."/".$theme_name;
    	}

    	if($theme_name){
    		if(!($theme = Mage::registry('theme_data'))){
    			if(!($_model = Mage::registry('ves_theme_model'))) {
    				$_model  = Mage::getModel('ves_tempcp/theme')->loadThemeByGroup($theme_name);
    				Mage::unregister('ves_theme_model');
    				Mage::register('ves_theme_model', $_model);
    			}
    			
				if($_model && $_model->getId()){
					$object = $this->bind( $_model );
					$_isset_theme_setting = true;
				}
				$params = $this->getParams();
    			$theme = $this->initTheme();
    			$theme_profile = $this->getProfile();

    			Mage::unregister('theme_profile');
    			Mage::register('theme_profile', $theme_profile);
    			Mage::register('theme_data', $theme);
    		}
    		if(!$check_exists || ($check_exists && $_isset_theme_setting)) {
    			return $theme;
    		}
    		
    	}
    	return false;
    }
    public function getTheme($themeId = 0){
		if($themeId){
			$_model  = Mage::getModel('ves_tempcp/theme')->load($themeId);
        	if ($_model->getId()) {
        		$_module_collection = Mage::getModel('ves_tempcp/module')->getModulesByTheme($_model->getId());
        		$this->modules = $_module_collection;

        		$object = $this->bind( $_model );
        		Mage::register('theme_data', $this);
        		Mage::register('current_data', $_model);
        	}
		}
		if(empty($themeId)){
    		if($_model = Mage::registry('current_data')){
    			$object = $this->bind( $_model );
    		}
    	}
    	$params = $this->getParams();
    	return $this;
	}

	public function bind($data = null){

		if(is_array($data) && !empty($data)){
			$theme = isset($data['theme'])?$data['theme']:array();
			$themecontrol = isset($data[$this->element_group])?$data[$this->element_group]:array();
			$theme_id = isset($data['theme_id'])?$data['theme_id']:0;

			if(!empty($theme)){
				foreach($theme as $key=>$val){
					$this->{$key} = $val;
				}
			}
			$this->params = $themecontrol;
			if($theme_id){
				$this->update_time = date("Y-M-d H:i:s");
			}else{
				$this->creation_time = date("Y-m-d H:i:s");
			}
		}elseif(is_object($data) && !empty($data)){
			$this->theme_id = $data->getThemeId();
			$this->title = $data->getTitle();
			$this->group = $data->getGroup();
			$tmp_theme = explode("/", $this->group);

            if(count($tmp_theme) == 1) {
                $this->group = "default/".$this->group;
            }
			$this->is_active = $data->getIsActive();
			$this->creation_time = $data->getCreationTime();
			$this->update_time = $data->getUpdateTime();
			$this->store_id = $data->getStoreId();

			$params = $data->getParams();

			$this->params = unserialize(base64_decode($params));

		}else{
			return false;
		}
		return $this;
		
	}

	public function getProfile() {
		return $this->config_ini;
		
	}
    /**
	 *
	 */
    public function getParams(){
    	$params = $this->get("params", array());
    	return $params;
    	/*
    	if(!empty($params)){
    		foreach($params as $key=>$val){
    			if(!empty($val) && $val != 0)
    				$this->$key = $val;
    		}
    	}
    	return $this;*/
    }

    public function getThemeSkins( $theme_path = "") {
    	if($theme_path) {
			$css_folder_path = $theme_path.'/css/skins/';
    	} else {
    		$css_folder_path = $this->theme_path.'/css/skins/';
    	}
    	if(!file_exists($css_folder_path)) {
    		$tmp_path = explode("/", $this->theme_path);
    		unset($tmp_path[count($tmp_path) - 1]);
    		$css_folder_path = implode("/", $tmp_path)."/default/css/skins/";
	    }

	    $dir_handle = @opendir($css_folder_path) or die("Unable to open $css_folder_path");
	    
	    $skins = array();
	    $skins[] = "default";
	    //display the target folder.
	    while (false !== ($file = readdir($dir_handle)))
	    {
	        if($file!="." && $file!="..")
	        {
	            if (is_dir($css_folder_path."/".$file) && $file != ".svn")
	            {
	                $skins[] = $file;
	            }
	        }
	    }
	    if(count($skins) == 1) {
	    	$tmp_path = explode("/", $this->theme_path);
    		unset($tmp_path[count($tmp_path) - 1]);
    		$css_folder_path = implode("/", $tmp_path)."/default/css/skins/";

    		$dir_handle = @opendir($css_folder_path) or die("Unable to open $css_folder_path");
    		
		    //display the target folder.
		    while (false !== ($file = readdir($dir_handle)))
		    {
		        if($file!="." && $file!="..")
		        {
		            if (is_dir($css_folder_path."/".$file) && $file != ".svn")
		            {
		                $skins[] = $file;
		            }
		        }
		    }

	    }
		return $skins;
    }
	/**
	 *
	 */
    public function getDefaultThemeConfig(){

		$config_xml = $this->theme_path.'/config.xml';
		$config_ini = $this->theme_path.'/etc/config.ini';
		$theme_name = "";
		$positions = $skins = $config = $modules = $modules_query = $samples = $header_layouts = $custom_settings = $internal_modules = array();
		$header_layouts["default"] = Mage::helper("ves_tempcp")->__("Default");

		/*get config from xml file*/
		if( file_exists($config_xml) ){
			$xmlObj = new Varien_Simplexml_Config($config_xml);
			$info = $xmlObj->getNode();

			//$info = simplexml_load_file( $config_xml, 'SimpleXMLElement', LIBXML_NOCDATA );
			$theme_name = isset($info->name)?$info->name:"";

			/*Module Export*/
			$module_export = isset($info->export)?$info->export: null;

			$theme_default_name = $this->get("theme");
			$tmp_theme = explode("/", $theme_default_name);
			$tmp_theme_default = $theme_default_name;
	        if(count($tmp_theme) > 1) {
	            $tmp_theme_default = $tmp_theme[0]."/default";
	        }

			if(file_exists(Mage::getBaseDir('skin') . '/frontend/'.$tmp_theme_default."/export.xml")) {
	            $export_xml = Mage::getBaseDir('skin') . '/frontend/'.$tmp_theme_default."/export.xml";
	            $xmlObj2 = new Varien_Simplexml_Config($export_xml);
				$export_info = $xmlObj2->getNode();
				$module_export = isset($export_info->export)?$export_info->export: null;
	        }

			/*get Positions*/
			if(isset($info->positions) && is_object($info->positions)){
				$vars = get_object_vars($info->positions);
				$positions = $vars['position'];
			}

			/*get Skins*/
			$skins = $this->getThemeSkins();

			/*get Export Module Sample Data*/
			
			if($module_export){
				if(isset($module_export->modules) && is_object($module_export->modules)) {
                    $attributes = $module_export->modules->attributes();
                    $section = isset($attributes['section'])?trim($attributes['section']):"community";
                    $modules = $module_export->modules->module;
                    if($modules) {
                        foreach($modules as $module) {
                            $attributes = $module->attributes();
                            $name = isset($attributes['name'])?trim($attributes['name']):"";
                            $realname = (isset($attributes['realname']) && $attributes['realname'])?trim($attributes['realname']):$name;
                            $type = isset($attributes['type'])?trim($attributes['type']):"json";
                            $module_section = isset($attributes['section'])?trim($attributes['section']):$section;

                            if($name) {
                             	$existed = false;
                               	if(file_exists(Mage::getBaseDir('etc')."/modules/".$realname.".xml")) {
                               		$existed = true;
                               	}
                               	$module_config = Mage::getStoreConfig(strtolower($name));
                               	$samples[$name] = array('type' 			=> $type,
                               							'moduleName' 	=> $realname,
                               							'section' 		=> $module_section,
                               							'existed' 		=> $existed,
                               							'installed'		=> empty($module_config)?false:true,
                               							'extension_installed' => Mage::helper("ves_tempcp/exportSample")->checkModuleInstalled($realname)
                               							
                               							);
                            }
                            
                        }
                    }
                    
                }
                
                if(isset($module_export->modules_query) && is_object($module_export->modules_query)) {
                    $attributes = $module_export->modules_query->attributes();
                    $section = isset($attributes['section'])?trim($attributes['section']):"community";
                    $type = isset($attributes['type'])?trim($attributes['type']):"json";
                    $modules = $module_export->modules_query->module;
                    if($modules) {
                        foreach($modules as $module) {
                        	$module = trim($module);
                            if($module) {
                               $modules_query[$module] = $module;
                            }
                            
                        }
                    }
                    
                }
			}
			
			if(isset($info->header_layout) && is_object($info->header_layout)) {
                    $layouts = $info->header_layout->layout;
                    if($layouts) {
                        foreach($layouts as $layout) {
                            $attributes = $layout->attributes();
                            $layout_name = isset($attributes['value'])?trim($attributes['value']):"";
                            if($layout_name) {
	                            $header_layouts[$layout_name] = trim($layout);
	                        }
                        }
                    }
                    
            }

			/*get font sizes*/
			if(isset($info->fontsizes) && is_object($info->fontsizes)){
				$vars = get_object_vars($info->fontsizes);
				$this->fontsizes = $vars['option'];
			}
			/*get fonts*/
			if(isset($info->fonts) && is_object($info->fonts)){
				$this->fonts = $this->getFieldList($info->fonts);
			}

			/*get custom fonts*/
			$this->custom_fonts = array();
			if(isset($info->custom_fonts) && is_object($info->custom_fonts)){
                    $fonts = $info->custom_fonts->font;
                    if($fonts) {
                        foreach($fonts as $font) {
                            $attributes = $font->attributes();
                            $store = isset($attributes['store'])?trim($attributes['store']):"";
                            
                            if($store) {
                             	$store_array = explode(",", $store);
                             	if(in_array($this->store_id, $store_array)) {
                             		$this->custom_fonts[] = trim($font);
                             	}
                            } else {
                            	$this->custom_fonts[] = trim($font);
                            }
                            
                        }
                    }
			}
			
			/*get default config*/
			$default = isset($info->default)?$info->default:null;
			if(is_object($default)){
				$vars = get_object_vars($default);
				$config = $vars;
			}

			/*get custom options from theme config*/
			if(isset($info->custom_settings) && is_object($info->custom_settings)){
				$custom_settings = $this->getCustomSettings( $info->custom_settings );
			}
			/*get internal modules*/
			if(isset($info->internal_modules) && is_object($info->internal_modules)){
				$internal_modules = $this->getFieldSets( $info->internal_modules );
			}

		}

		/*get default theme option*/
		if(file_exists($config_ini)){
			$this->config_ini = Mage::getSingleton("ves_tempcp/profile")->getInstance( $this->get("theme"), $this->get("default_profile") );
		}

		$this->theme_name = $theme_name;
		$this->positions = $positions;
		$this->custom_settings = $custom_settings;
		$this->default_config = $config;
		$this->skins	 = $skins;
		$this->internal_modules = $internal_modules;
		$this->modules_query = $modules_query;
		$this->samples = $samples;
		$this->header_layouts = $header_layouts;

		//if($this->default_config)
		//	$this->setDefaultConfig( $this->default_config );

		return $this->default_config;
	}

	/**
	 *
	 */
    public function initTheme() {

    	//$this->getTheme();
    	// themes 
		$directories = glob(Mage::getBaseDir('skin') . '/frontend/*/*', GLOB_ONLYDIR);
		$this->templates = array();

		foreach ($directories as $directory) {
			if( file_exists($directory."/etc/config.ini") ){
				$tmp = explode("/", $directory);
				$package = $tmp[count($tmp)-2];
				$theme_name = basename($directory);

				$this->templates[] = $package."/".$theme_name;
			}
		}

		$default_theme = "";
		if(isset($this->params['default_theme']) && $this->params['default_theme']){
			$default_theme = $this->params['default_theme'];
		}elseif(count($this->templates)){
			$default_theme = $this->templates[0];
		}
		$tmp_check = explode("/", $default_theme);
		if(count($tmp_check) == 1) {
			$default_theme = "default/".$default_theme;
		}
		$this->set( "theme", $default_theme );
		
		$base = Mage::getBaseUrl();
		$base = str_replace("/index.php","", $base);
		$this->theme_url = $base. 'skin/frontend/'.$this->get("theme");
		$this->theme_path 	 	 = Mage::getBaseDir('skin') . '/frontend/'.$this->get("theme");

		$this->patterns 	= $this->getPattern();

		$this->getDefaultThemeConfig();

		$this->checkingInfo();

    	return $this;
    }
    public function setDefaultConfig( $default = array() ){
    	if(!empty($default)){
    		foreach($default as $key=>$val){
    			if(!isset($this->$key) || (isset($this->$key) && empty($this->$key)) ){
    				$this->$key = $val;
    			}
    		}
    	}
    }
    public function checkingInfo(){

    }
    /**
	 * 
	 */
	public function getPattern(){
		$output = array();
		if( $this->get("theme_path") && is_dir($this->get("theme_path").'/images/pattern/') ) {
			$files = glob($this->get("theme_path").'/images/pattern/*');
			foreach( $files as $dir ){
				if( preg_match("#.png|.jpg|.gif#", $dir)){
					$output[] = str_replace("","",basename( $dir ) );
				}
			}			
		}
		return $output;
	}
	protected function getFieldList($objxml = null){
    	$tmp = array();
    	if(!empty($objxml)){
    		foreach($objxml->children() as $key=>$child){
					$attributes = $child->attributes();
					$value = isset($attributes['value'])?(string)$attributes['value']: '';
					$text = (string) $child;
					$tmp[$value] = $text;
			}
    	}
    	return $tmp;
    }
    protected function getFieldSets($objxml = null){
    	$tmp = array();
    	if(!empty($objxml)){
    		$fields = $objxml->fields;
    		if(!empty($fields)){

    			foreach($fields as $field_item){
    				$positions = array();
    				$attributes = $field_item->attributes();
    				$position = isset($attributes["name"])?$attributes["name"]:"";

    				if($array_fieldsets = $field_item->fieldset){
    					foreach($array_fieldsets as $key=>$fieldset_item){
    						$attributes = $fieldset_item->attributes();
    						$array_fields = array();
    						foreach($fieldset_item->field as $key => $element){
    							$element_type = isset($element['type'])?(string)$element['type']:'label';
    							$field_path = "";
    							if(file_exists(dirname(__FILE__)."Ves_Tempcp_Helper_fields_".$element_type.".php") ) {
    								$field_path = dirname(__FILE__)."Ves_Tempcp_Helper_fields_".$element_type.".php";
    							} elseif(file_exists(VES_TEMCP_HELPER_PATH."fields/".$element_type.".php")) {
    								$field_path = VES_TEMCP_HELPER_PATH."fields/".$element_type.".php";
    							}

    							if($field_path){
    								
									require_once($field_path);

									$class_name = "Field".ucfirst($element_type);
									if(class_exists($class_name)){
										$array_fields[] = new $class_name( $element, (string)$element, $this->element_group);
									}
								}
    						}
    						if(isset($attributes['name'])){
    							$positions[ (string)$attributes['name']] = $array_fields;
    						}else{
    							$positions[] = $array_fields;
    						}

    					}
    				}

    				$tmp[ (string)$position ] = $positions;
    			}
    		}
    	}

    	return $tmp;
    }

    protected function getCustomSettings($objxml = null){
    	$tmp = array();
    	if(!empty($objxml)){
    		$tabs = $objxml->tab;
    		if(!empty($tabs)){

    			foreach($tabs as $tab_item){
    				$tab_settings = array();
    				$attributes = $tab_item->attributes();
    				$tab_name = isset($attributes["name"])?$attributes["name"]:"";

    				if($array_subtabs = $tab_item->sub_tab){
    					foreach($array_subtabs as $key=>$sub_tab_item){
    						$attributes = $sub_tab_item->attributes();
    						$array_fields = array();
    						foreach($sub_tab_item->field as $key => $element){
    							$element_type = isset($element['type'])?(string)$element['type']:'label';
    							$field_path = "";
    							if(file_exists(dirname(__FILE__)."Ves_Tempcp_Helper_fields_".$element_type.".php") ) {
    								$field_path = dirname(__FILE__)."Ves_Tempcp_Helper_fields_".$element_type.".php";
    							} elseif(file_exists(VES_TEMCP_HELPER_PATH."fields/".$element_type.".php")) {
    								$field_path = VES_TEMCP_HELPER_PATH."fields/".$element_type.".php";
    							}

    							if($field_path){
    								
									require_once($field_path);

									$class_name = "Field".ucfirst($element_type);
									if(class_exists($class_name)){
										$array_fields[] = new $class_name( $element, (string)$element, $this->element_group);
									}
								}
    						}
    						if(isset($attributes['name'])){
    							$tab_settings[ (string)$attributes['name']] = $array_fields;
    						}else{
    							$tab_settings[] = $array_fields;
    						}

    					}
    				} else {
    					$array_fields = array();
    					foreach($tab_item->field as $key => $element){
							$element_type = isset($element['type'])?(string)$element['type']:'label';
							$field_path = "";
							if(file_exists(dirname(__FILE__)."Ves_Tempcp_Helper_fields_".$element_type.".php") ) {
								$field_path = dirname(__FILE__)."Ves_Tempcp_Helper_fields_".$element_type.".php";
							} elseif(file_exists(VES_TEMCP_HELPER_PATH."fields/".$element_type.".php")) {
								$field_path = VES_TEMCP_HELPER_PATH."fields/".$element_type.".php";
							}

							if($field_path){
								
								require_once($field_path);

								$class_name = "Field".ucfirst($element_type);
								if(class_exists($class_name)){
									$array_fields[] = new $class_name( $element, (string)$element, $this->element_group);
								}
							}
    					}
    					$tab_settings = $array_fields;
    				}

    				$tmp[ (string)$tab_name ] = $tab_settings;
    			}
    		}
    	}

    	return $tmp;
    }
}
/**
* Class Tempcp Theme - extends Theme Core
* Use the class to get theme information
*
*/
class Ves_Tempcp_Helper_Theme extends Tempcp_Theme_Core {

	var $theme_id = 0;
	var $group = "";
	var $creation_time = "";
	var $update_time = "";
	var $skins = array();
	var $default_config = array();

	public function initTheme() {
		return parent::initTheme();

	}
	
	public function checkingInfo(){
		
	}

	/**
	 *
	 */
	public function writeToCache( $folder, $file, $value, $e='css' ){
		$file = $folder  . preg_replace('/[^A-Z0-9\._-]/i', '', $file).'.'.$e ;
		$handle = fopen($file, 'w');
    	fwrite($handle, ($value));
		
    	fclose($handle);

	}
	/**
	 *
	 */
	private function getFileList( $path , $e=null ) {
		$output = array(); 
		$directories = glob( $path.'*'.$e );
		foreach( $directories as $dir ){
			$output[] = basename( $dir );
		}			
		 
		return $output;
	}
	
	/**
	 *
	 */
	public function getConfig( $config ){
		return ''.$config;
	}
}
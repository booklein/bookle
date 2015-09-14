<?php
 /*------------------------------------------------------------------------
  # VenusTheme Block Builder Module
  # ------------------------------------------------------------------------
  # author:    VenusTheme.Com
  # copyright: Copyright (C) 2012 http://www.venustheme.com. All Rights Reserved.
  # @license: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.venustheme.com
  # Technical Support:  http://www.venustheme.com/
-------------------------------------------------------------------------*/
class Ves_BlockBuilder_Helper_Data extends Mage_Core_Helper_Abstract {
    
    public function getShortCode($key, $alias = "", $settings = array()) {
        if($key) {
            $options = array();
            if($settings) {
                foreach($settings as $k => $v) {
                    if(trim($v)) {
                        $options[] = trim($k). '="'.trim($v).'"';
                    }
                }
            }
            $block_id = '';
            if($alias) {
                $block_id = 'block_id="'.trim($alias).'"';
            }
            return '{{widget type="'.trim($key).'" '.$block_id.' '.implode(" ", $options).'}}';
        }
        return  ;
    }

    public function generateBlockBuilder($alias = "") {
        if($alias) {
            $short_code = $this->getShortCode("ves_blockbuilder/widget_builder", $alias);
            $processor = Mage::helper('cms')->getPageTemplateProcessor();
            return $processor->filter($short_code);
        }
        return ;
    }

     public function runShortcode($short_code = "") {
        if($short_code) {
            $processor = Mage::helper('cms')->getPageTemplateProcessor();
            return $processor->filter($short_code);
        }
        return ;
    }

    public function checkModuleInstalled( $module_name = "") {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;
        if($modulesArray) {
            $tmp = array();
            foreach($modulesArray as $key=>$value) {
                $tmp[$key] = $value;
            }
            $modulesArray = $tmp;
        }

        if(isset($modulesArray[$module_name])) {

            if((string)$modulesArray[$module_name]->active == "true") {
                return true;
            } else {
                return false;
            }
            
        } else {
            return false;
        }
    }

    public function getWidgetFormUrl($target_id = "") {
        $params = array();
        if($target_id) {
            $params['widget_target_id'] = $target_id;
        }

        $admin_route = Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
        $admin_route = $admin_route?$admin_route:"admin";

        $url = Mage::getSingleton('adminhtml/url')->getUrl('*/widget/loadOptions', $params);
        $url = str_replace("/blockbuilder/","/{$admin_route}/", $url);
        return $url;
    }

    public function getListWidgetsUrl($target_id = "") {
        //return Mage::helper("adminhtml")->getUrl("*/*/listwidgets"); 
        $params = array();
        if($target_id) {
            $params['widget_target_id'] = $target_id;
        }

        $admin_route = Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
        $admin_route = $admin_route?$admin_route:"admin";
        
        $url = Mage::getSingleton('adminhtml/url')->getUrl('*/widget/index', $params);
        $url = str_replace("/blockbuilder/","/{$admin_route}/", $url);
        return $url;
    }

    public function getWidgetDataUrl() {
        return Mage::helper("adminhtml")->getUrl("*/*/widgetdata");
    }

    public function getImageUrl($secure = false) {
        if($secure) {
            return str_replace(array('index.php/', 'index.php'), '', Mage::getBaseUrl('media', true));
        } else {
            return str_replace(array('index.php/', 'index.php'), '', Mage::getBaseUrl('media', false));
        }
        
    }

    /**
     * Handles CSV upload
     * @return string $filepath
     */
    public function getUploadedFile( $profile = "", $is_pagebuilder = false, $sub_folder = "") {
        $filepath = null;

        if(isset($_FILES['importfile']['name']) and (file_exists($_FILES['importfile']['tmp_name']))) {
            try {

                $uploader = new Varien_File_Uploader('importfile');
                $uploader->setAllowedExtensions(array('csv','txt', 'json', 'xml')); // or pdf or anything
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);

                $path = Mage::helper('ves_blockbuilder')->getImportPath();
                $file_type = "csv";
                if($_FILES['importfile']['tmp_name']['type'] == "application/json") {
                    $file_type = "json";
                }
                $uploader->save($path, "ves_pagebuilder_sample_data.".$file_type);
                $filepath = $path . "ves_pagebuilder_sample_data.".$file_type;

            } catch(Exception $e) {
                // log error
                Mage::logException($e);
            } // end if

        } // end if
        elseif($profile) {
            $profile = $sub_folder?$sub_folder."/".$profile:$profile;
            if($is_pagebuilder) {
                $filepath = Mage::getBaseDir('media')."/pagebuilder/page_profiles/".$profile;
                if(!file_exists($filepath)) {
                    $filepath = false;
                }
            } else {

                $filepath = Mage::getBaseDir('media')."/pagebuilder/block_profiles/".$profile;
                if(!file_exists($filepath)) {
                    $filepath = false;
                }
            }
            
        }
        return $filepath;

    }

    public function getImportPath($theme = ""){
        $path = Mage::getBaseDir('var') . DS . 'cache'.DS;

        if (is_dir_writeable($path) != true) {
            mkdir ($path, '0744', $recursive  = true );
        } // end

        return $path;
    }
    public function getAllStores() {
        $allStores = Mage::app()->getStores();
        $stores = array();
        foreach ($allStores as $_eachStoreId => $val) 
        {
            $stores[]  = Mage::app()->getStore($_eachStoreId)->getId();
        }
        return $stores;
    }

    /**
     *
     */
    public function getFileList( $path , $e=null, $filter_pattern = "" ) {
        $output = array();
        $directories = glob( $path.'*'.$e );
        if($directories) {
            foreach( $directories as $dir ){
                if($filter_pattern) {
                    $file_name = basename( $dir );
                    if(strpos($file_name, $filter_pattern) !== false) {
                        $output[] = basename( $dir );
                    }
                    
                } else {
                    $output[] = basename( $dir );
                }
                
            }  
        }
                 
        
        return $output;
    }

     /**
     *
     */
    public function getBlockProfiles() {
        $path = Mage::getBaseDir('media')."/pagebuilder/block_profiles/";
        $dirs = array_filter(glob($path . '/*'), 'is_dir');
        $file_type = ".csv";
        $output = array();
        if($dirs) {
            $output["general"] = Mage::helper("ves_blockbuilder")->getFileList($path, $file_type);
            foreach($dirs as $dir) {
                $file_name = basename( $dir );
                $tmp_path = $path.$file_name."/";
                if($tmp_output = Mage::helper("ves_blockbuilder")->getFileList($tmp_path, $file_type)){
                    $output[$file_name] = $tmp_output;
                }

            }

        } else {
            $output = Mage::helper("ves_blockbuilder")->getFileList($path, $file_type);
        }

        return $output;
    }
    /**
     *
     */
    public function getPageProfiles() {
        $path = Mage::getBaseDir('media')."/pagebuilder/page_profiles/";
        $dirs = array_filter(glob($path . '/*'), 'is_dir');
        $file_type = ".csv";
        $output = array();
        if($dirs) {
            $output["general"] = Mage::helper("ves_blockbuilder")->getFileList($path, $file_type);
            foreach($dirs as $dir) {
                $file_name = basename( $dir );
                $tmp_path = $path.$file_name."/";
                if($tmp_output = Mage::helper("ves_blockbuilder")->getFileList($tmp_path, $file_type)){
                    $output[$file_name] = $tmp_output;
                }

            }
        } else {
            $output = Mage::helper("ves_blockbuilder")->getFileList($path, $file_type);
        }
        
        return $output;
    }

    public function getBlockProfilePath( $profile = "") {
        $path = Mage::getBaseDir('media')."/pagebuilder/block_profiles/".$profile.".csv";

        if(file_exists($path)) {
            return $path;
        }

        return false;
        
    }

    public function getPageProfilePath( $profile = "") {
        $path = Mage::getBaseDir('media')."/pagebuilder/page_profiles/".$profile.".csv";

        if(file_exists($path)) {
            return $path;
        }

        return false;
    }

    /**
     *
     */
    public function writeToCache( $folder, $file, $value, $e='css' ){
        $file = $folder  . preg_replace('/[^A-Z0-9\._-]/i', '', $file).'.'.$e ;
        if (file_exists($file)) {
            unlink($file);
        }

        $flocal = new Varien_Io_File();
        $flocal->open(array('path' => $folder));
        $flocal->write($file, $value);
        $flocal->close();
        @chmod($file, 0755);
    }
    
    public function getThemeCustomizePath($theme = "") {
        $tmp_theme = explode("/", $theme);
        if(count($tmp_theme) == 1) {
            $theme = "base/".$theme;
        }
        $customize_path = Mage::getBaseDir('skin')."/frontend/".$theme."/css/customize/";
        if(!file_exists($customize_path)) {
            $file = new Varien_Io_File();
            $file->mkdir($customize_path);
            $file->close();
        }
        return $customize_path;
    }


    public function getSelectorGroups () {
        return array(   'body' => Mage::helper("ves_blockbuilder")->__('Body Content'),
                        'topbar' => Mage::helper('ves_blockbuilder')->__('TopBar'),
                        'header-main' => Mage::helper('ves_blockbuilder')->__('Header'),
                        'mainmenu' => Mage::helper('ves_blockbuilder')->__('MainMenu'),
                        'footer' => Mage::helper('ves_blockbuilder')->__('Footer'),
                        'footer-top' => Mage::helper('ves_blockbuilder')->__('Footer Top'),
                        'footer-center' => Mage::helper('ves_blockbuilder')->__('Footer Center'),
                        'footer-bottom' => Mage::helper('ves_blockbuilder')->__('Footer Bottom'),
                        'product' => Mage::helper('ves_blockbuilder')->__('Products'),
                        'powered' => Mage::helper('ves_blockbuilder')->__('Modules in Sidebar'),
                        'module-sidebar' => Mage::helper('ves_blockbuilder')->__('Modules in Sidebar'),
                        'module-block' => Mage::helper('ves_blockbuilder')->__('Module Blocks'),
                        'cart-block' => Mage::helper('ves_blockbuilder')->__('Cart Blocks'),
                        'checkout' => Mage::helper('ves_blockbuilder')->__('Checkout'),
                        'custom' => Mage::helper('ves_blockbuilder')->__('Custom')
                        );
    }

    public function getSelectorTypes () {
        return array('raw-text' => Mage::helper("ves_blockbuilder")->__('Text'),
                    'text' => Mage::helper("ves_blockbuilder")->__('Color Input'),
                    'image' => Mage::helper('ves_blockbuilder')->__('Image Pattern'),
                    'fontsize' => Mage::helper('ves_blockbuilder')->__('Font Size'),
                    'borderstyle' => Mage::helper('ves_blockbuilder')->__('Border Style'),
                    'textarea' => Mage::helper("ves_blockbuilder")->__('Custom Css Code'),
                );
    }
}

?>
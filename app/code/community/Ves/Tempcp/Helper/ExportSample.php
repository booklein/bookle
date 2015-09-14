<?php

class Ves_Tempcp_Helper_ExportSample extends Mage_Core_Helper_Abstract {

    var $export_static_blocks = "static_blocks.csv";
    var $export_cms_pages = "cms_pages.csv";
    var $export_cms_page_text = "cms_pages.txt";
    var $export_static_block_text = "static_blocks.txt";
    var $export_ves_tempcp = "ves_tempcp.csv";
    var $export_websites = "websites.csv";
    var $export_store_groups = "store_groups.csv";
    var $export_stores = "stores.csv";
    var $export_widgets = "Mage_Widget.json";
    var $export_default_homepage = "default_cms_homepage.json";

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
    /**
    * Write Sample Data to File. Store in folder: "skin/frontend/default/ves theme name/import/"
    */
    public function writeSampleDataFile($importDir, $file_name, $content = "") {

        $file = new Varien_Io_File();
        //Create import_ready folder
        $error = false;
        if(!file_exists($importDir)) {
            $importReadyDirResult = $file->mkdir($importDir);
            $error = false;
            if (!$importReadyDirResult) {
                //Handle error
                $error = true;
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ves_tempcp')->__('Can not create folder "%s".', $importDir));
            }
        } else {
            $file->open(array('path' => $importDir));
        }

        if (!$file->write($importDir.$file_name, $content)) {
            //Handle error
            $error = true;
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ves_tempcp')->__('Can not save import sample file "%s".', $file_name));
        }

        if(!$error) {
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('Successfully, Stored sample data file "%s".', $file_name));
        }

        return !$error;
    }
   
    /**
    *
    *Read config.xml in skin folder of the theme to get export information before export sample data
    **/
    public function readExportInfo( $theme_name = "", $section = "default" ) {
        $tmp_theme = explode("/", $theme_name);
        $tmp_theme_default = "";
        if(count($tmp_theme) == 1) {
            $theme_name = $section."/".$theme_name;
            $tmp_theme_default = $theme_name;
        } else {
            $tmp_theme_default = $tmp_theme[0]."/default";
        }
        $theme_path = Mage::getBaseDir('skin') . '/frontend/'.$theme_name;
        $config_xml = $theme_path.'/config.xml';
        if(file_exists(Mage::getBaseDir('skin') . '/frontend/'.$tmp_theme_default."/export.xml")) {
            $config_xml = Mage::getBaseDir('skin') . '/frontend/'.$tmp_theme_default."/export.xml";
        }

        $result = array();
        /*get config from xml file*/
        if( file_exists($config_xml) ){
            $info = simplexml_load_file( $config_xml, 'SimpleXMLElement', LIBXML_NOCDATA );
            /*get Export Sample Data*/
            if(isset($info->export)){
                if(isset($info->export->theme)) {
                    $result['theme'] = (string)$info->export->theme;
                }

                if(isset($info->export->cms_page)) {
                    $result['cms_page'] = (string)$info->export->cms_page;
                }

                if(isset($info->export->static_block)) {
                    $result['static_block'] = (string)$info->export->static_block;
                }

                if(isset($info->export->modules) && is_object($info->export->modules)) {
                    $attributes = $info->export->modules->attributes();
                    $section = isset($attributes['section'])?trim($attributes['section']):"community";
                    $modules = $info->export->modules->module;
                    $result['modules'] = array();
                    if($modules) {
                        foreach($modules as $module) {
                            $attributes = $module->attributes();
                            $name = isset($attributes['name'])?trim($attributes['name']):"";
                            $realname = (isset($attributes['realname']) && $attributes['realname'])?trim($attributes['realname']):$name;
                            $type = isset($attributes['type'])?trim($attributes['type']):"json";
                            $module_section = isset($attributes['section'])?trim($attributes['section']):$section;

                            if($name) {
                               $result['modules'][$name] = array();
                               $result['modules'][$name]['section'] = $module_section;
                               $result['modules'][$name]['type'] = $type;
                               $result['modules'][$name]['realname'] = $realname;
                               $tmp = trim($module);
                               $tmp = str_replace("\n",",", $tmp);
                               $tmp_array1 = $tmp_array2 = array();
                               if($tmp) {
                                    $tmp_array1 = explode(",", $tmp);
                               }
                               
                               

                               $sql_tables = $this->getModuleTables($name);
                               if($sql_tables) {
                                    $tmp_array2 = $sql_tables;
                               }

                               $tmp_array = array_merge($tmp_array1, $tmp_array2);
                               

                               $result['modules'][$name]['tables'] = $tmp_array;

                            }
                            
                        }
                    }
                    
                }
                
            }
        }
        return $result;
    }
    public function getCmsPageInfomation() {
        $collection = Mage::getResourceModel('cms/page_collection');
        $page_layouts = Mage::getSingleton('page/config')->getPageLayouts();

        $content = "";
        // add the stores this block belongs to
        $i = 1;
        foreach ($collection as $key => $page) {
            $stores = $page->getResource()->lookupStoreIds($page->getPageId());
          
        
            if($stores) {
                $tmp = array();
                foreach($stores as $storeId ){
                    if($storeId) {
                        $website_id = Mage::getModel('core/store')->load($storeId)->getWebsiteId();
                        $website = Mage::app()->getWebsite($website_id);
                        $website_name = $website->getName();
                        $tmp[] = $website_name." > ".Mage::getModel('core/store')->load($storeId)->getName();
                    }
                }
                if($tmp)
                    $stores = $tmp;
            }
            $stores = implode(', ', $stores);
            $stores = !$stores?"All":$stores;
            $status = $page->getIsActive();
            $status = $status?"enabled":"disabled";

            $root_template = $page->getRootTemplate();
            if(isset($page_layouts[$root_template]) && $page_layouts[$root_template]) {
                $root_template = $page_layouts[$root_template]->getLabel();
            }

            if($i == 1) {
                $content .= "----------------------------------------------------------";
                $content .= "\nCMS Page ".$i;
            } else {
                $content .= "\n\n\n----------------------------------------------------------";
                $content .= "\nCMS Page ".$i;
            }
            
            $content .= "\n----------------------------------------------------------";
            $content .= "\nPage Title:   ".$page->getTitle();
            $content .= "\nURL Key:   ".$page->getIdentifier();
            $content .= "\nStore View:   ".$stores;
            $content .= "\nStatus:       ".$status;
            $content .= "\nContent: \n".$page->getContent();
            $content .= "\n\nLayout:     ".$root_template;
            $content .= "\nLayout Update XML: \n".$page->getLayoutUpdateXml();

            $i++;
        } // end

        return $content;
    }
    public function getCmsBlockInfomation() {
        //$block_collection = Mage::getModel('cms/block')->getCollection();
        $block_collection = Mage::getResourceModel('cms/block_collection');
        $content = "";

        // add the stores this block belongs to
        $i = 1;
        foreach ($block_collection as $key => $block) {
            $stores = $block->getResource()->lookupStoreIds($block->getBlockId());
            if($stores) {
                $tmp = array();
                foreach($stores as $storeId ){
                    if($storeId) {
                        $website_id = Mage::getModel('core/store')->load($storeId)->getWebsiteId();
                        $website = Mage::app()->getWebsite($website_id);
                        $website_name = $website->getName();
                        $tmp[] = $website_name." > ".Mage::getModel('core/store')->load($storeId)->getName();
                    }
                }
                if($tmp)
                    $stores = $tmp;
            }
            $stores = implode(', ', $stores);
            $stores = !$stores?"All":$stores;
            $status = $block->getIsActive();
            $status = $status?"enabled":"disabled";

            if($i == 1) {
                $content .= "----------------------------------------------------------";
                $content .= "\nStatic Block ".$i;
            } else {
                $content .= "\n\n\n----------------------------------------------------------";
                $content .= "\nStatic Block ".$i;
            }
            $content .= "\n----------------------------------------------------------";
            $content .= "\nBlock Title:    ".$block->getTitle();
            $content .= "\nIdentifier:     ".$block->getIdentifier();
            $content .= "\nStore View:     ".$stores;
            $content .= "\nStatus:         ".$status;
            $content .= "\nContent: \n".$block->getContent();

            $i++;
        } // end


        return $content;
    }

    public function export($theme_name = "", $export_mode = "full", $stores = array()) {

        if($theme_name) {
            $tmp_theme = explode("/", $theme_name);
            if(count($tmp_theme) == 1) {
                $theme_name = "default/".$theme_name;
            }
            $export_list = $this->readExportInfo( $theme_name );


            $importDir = Mage::getBaseDir('skin') . '/frontend/'.$theme_name.'/import/';

            $module_import_dir = $importDir.'modules/';
            $cms_import_dir = $importDir.'cms/';

            if($export_mode == "setting") {
                $importDir = Mage::getBaseDir('cache') ."/backup_".str_replace( "/", "_", $theme_name).'/';
                $module_import_dir = $importDir;
            }
           
            
            /*Export modules*/
            if(isset($export_list['modules'])) {
                foreach($export_list['modules'] as $key => $module) {
                    if($module) {
                        $type = isset($module['type'])?$module['type']:"json";
                        $realname = (isset($module['realname']) && $module['realname'])?$module['realname']:$key;
                        $tables = isset($module['tables'])?$module['tables']:array();
                        if($tables) {
                            $tmp = array();
                            foreach($tables as $table) {
                                $table = trim($table);
                                if(!empty($table)) {
                                    $tmp[] = $key."/".trim($table);
                                }
                            }
                            $tables = $tmp;
                        }

                        if( $module_sample_data = $this->exportSample( $realname, $tables, $type, $export_mode, $stores ) ) {
                            $this->writeSampleDataFile( $module_import_dir, $realname.".".$type, $module_sample_data);
                        }
                    }
                }
            }
            if($export_mode == "full") {
                /*Export static blocks*/
                //if(isset($export_list['stores'])) {
                /*
                    $content    = Mage::app()->getLayout()->createBlock('ves_tempcp/adminhtml_cms_enhanced_store_grid')->getCsvFile($this->export_stores);
                    
                    $store_content = "";
                    if(!empty($content) && isset($content['value']) && file_exists($content['value'])) {
                        $store_content = file_get_contents($content['value']);
                    }
                    if($store_content) {
                        $this->writeSampleDataFile( $importDir, $this->export_stores, $store_content);
                    }
                */
                //}
                /*Export cms pages*/
                //if(isset($export_list['cms_page'])) {

                    $content    = Mage::app()->getLayout()->createBlock('ves_tempcp/adminhtml_cms_enhanced_page_grid')->getCsvFile($this->export_cms_pages);
                    $cms_content = "";
                    if(!empty($content) && isset($content['value']) && file_exists($content['value'])) {
                        $cms_content = file_get_contents($content['value']);
                    }
                    if($cms_content) {
                        $this->writeSampleDataFile( $cms_import_dir, $this->export_cms_pages, $cms_content);
                    }


                //}
                /*Export static blocks*/
                //if(isset($export_list['static_block'])) {

                    $content  = Mage::app()->getLayout()->createBlock('ves_tempcp/adminhtml_cms_enhanced_block_grid')->getCsvFile($this->export_static_blocks);

                    $static_content = "";
                    if(!empty($content) && isset($content['value']) && file_exists($content['value'])) {
                        $static_content = file_get_contents($content['value']);
                    }
                    if($static_content) {
                        $this->writeSampleDataFile( $cms_import_dir, $this->export_static_blocks, $static_content);
                    }

                //}

                /*Export cms pages text*/
                $cms_page_content = $this->getCmsPageInfomation();
                if($cms_page_content) {
                    $this->writeSampleDataFile( $cms_import_dir, $this->export_cms_page_text, $cms_page_content);
                }

                /*Export cms blocks text*/
                $cms_block_content = $this->getCmsBlockInfomation();
                if($cms_block_content) {
                    $this->writeSampleDataFile( $cms_import_dir, $this->export_static_block_text, $cms_block_content);
                }
                

                /*Export theme setting*/
                //if(isset($export_list['theme'])) {

                    $content    = Mage::app()->getLayout()->createBlock('ves_tempcp/adminhtml_cms_enhanced_theme_grid')->getCsvFile( $this->export_ves_tempcp );

                    $theme_content = "";

                    if(!empty($content) && isset($content['value']) && file_exists($content['value'])) {
                        $theme_content = file_get_contents($content['value']);
                    }

                    if($theme_content) {
                        $this->writeSampleDataFile( $importDir, $this->export_ves_tempcp, $theme_content);
                    }

                //}
                 /*Export widget*/
                $type = "json";
                $tables = array("widget/widget",
                                "widget/widget_instance",
                                "widget/widget_instance_page",
                                "widget/widget_instance_page_layout");
                
                if( $module_sample_data = $this->exportSample( "Mage_Widget", $tables, $type) ) {
                    $this->writeSampleDataFile( $module_import_dir, $this->export_widgets, $module_sample_data);
                }

                $data_settings = array();
                if($stores) {
                    $cms_home_page = "";
                    foreach($stores as $store_id) {
                       $cms_home_page = Mage::getStoreConfig("web/default/cms_home_page", $store_id); //array 
                       break;
                    }
                    $data_settings['config'] = $cms_home_page;
                    
                } else {
                    $cms_home_page = Mage::getStoreConfig("web/default/cms_home_page"); //array

                    $data_settings['config'] = array($cms_home_page);
                }
                if($result = Mage::helper('core')->jsonEncode($data_settings) ) {
                    $this->writeSampleDataFile( $importDir, $this->export_default_homepage, $result);
                }
                
            }
        }
    }
    /**
    * Export module sample data: support CSV and JSON
    * @module: Name of module which you want export data (for example: ves_megamenu)
    * @tables: List table name which you want export sample (for example a table name: ves_megamenu/megamenu)
    * @type: type of data which you want export
    * @return: return string of CSV or JSON
    **/
    public function exportSample($module = "", $tables = array(), $type = "json", $export_mode = "full", $stores = array()) {
        $stores = is_array($stores)?$stores: array((int)$stores);
        if(!$stores) {
            $stores[] = 0;
        }

        if(!$this->checkModuleInstalled($module)) 
            return false;

        $result = "";
        switch ($type) {
            case 'csv' :
            
            break;
            case 'sql' :
                
            break;
            case 'json' :
            default:
                $data = array();
                if($export_mode == "full") {
                    /**
                     * Get the resource model
                     */
                    $resource = Mage::getSingleton('core/resource');
                     
                    /**
                     * Retrieve the read connection
                     */
                    $readConnection = $resource->getConnection('core_read');
                    
                    if($tables) {
                        foreach ($tables as $table_name) {
                            $table_name = trim($table_name);
                            $table_name = strtolower($table_name);
                            $query = 'SELECT * FROM ' . $resource->getTableName($table_name);
                        
                            /**
                             * Execute the query and store the results in $results
                             */
                            $module_table = $readConnection->fetchAll($query);

                            $data[ $table_name ] = $module_table;
                        }
                    }
                }

                
                $module = strtolower($module);

                //$stores = $this->getListStores();
                if($stores) {
                    $config = array();
                    $config[0] = Mage::getStoreConfig($module); //array 
                    foreach($stores as $store_id) {
                       $config[$store_id] = Mage::getStoreConfig($module, $store_id); //array 
                    }
                    $data['config'] = $config;
                    
                } else {
                    $config = Mage::getStoreConfig($module); //array

                    $data['config'] = $config;
                }
                
                
                $result = Mage::helper('core')->jsonEncode($data);

                break;
        }
        
        return $result;
    }

    public function getListStores() {
        $result = array();
        $stores = Mage::app()->getStores();
        if(count($stores) > 1){

            foreach($stores as $store) {
                $result[] = $store->getId();
            }
        }
        return $result;
    }

    public function getModuleTables($module_name = "") {
        $sql_tables = array("Ves_Tempcp" => array("theme", "theme_store", "module"),
                            "Ves_Blog" => array("category", "category_store", "comment", "comment_store", "post", "post_store"),
                            "Ves_Brand" => array("brand", "brand_store"),
                            "Ves_Verticalmenu" => array("verticalmenu", "verticalmenu_store", "verticalmenu_widget"),
                            "Ves_Layerslider" => array("banner", "banner_store"),
                            "Ves_Contentslider" => array("banner", "banner_store"),
                            "Ves_Megamenu" => array("megamenu", "megamenu_store", "megamenu_widget"),
                            "Ves_BlockBuilder" => array("block", "template"),
                            "Ves_Landingpage" => array("slider", "slider_store"),
                            "Ves_Testimonial" => array("testimonial", "testimonial_store"),
                            "Ves_Artist" => array("group", "group_store", "artist", "artist_store"),
                            "Ves_FAQ" => array("category", "category_store", "question", "question_store", "answer", "answer_store"),
                            "Ves_Parallax" => array("banner", "banner_store"),
                            "Ves_Map" => array("group", "group_store", "location", "location_store"),
                            "Ves_Gallery" => array("banner")
                            );
        return isset($sql_tables[$module_name])?$sql_tables[$module_name]:false;
    }
}
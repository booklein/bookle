<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Ves * @package     Ves_Tempcp
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Theme controller
 *
 * @category   Ves
 * @package     Ves_Tempcp
 * @author      
 */


class Ves_Tempcp_Adminhtml_ThemeController extends Mage_Adminhtml_Controller_Action {
    var $destination_filename = "ves_tempcp.csv";
    var $import_static_blocks = "static_blocks.csv";
    var $import_cms_pages = "cms_pages.csv";
    var $import_ves_tempcp = "ves_tempcp.csv";
    var $import_stores = "stores.csv";
    var $cms_home_page = "default_cms_homepage.json";

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('tempcp');

        return $this;
    }

    public function indexAction() {
        $this->_title($this->__('Ves Themes Manager'));
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('ves_tempcp/adminhtml_theme'));
        $sort = $this->getRequest()->getParam('sort');
        if($sort == "group") {
            $this->getRequest()->setParam('sort','main_table.group');
            $_GET['sort'] = 'main_table.group';
        }
        $this->renderLayout();
        
    }

    public function datasampleAction() {
        $this->_title($this->__('Data Sample Manager - Venus Theme'));
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('ves_tempcp/adminhtml_datasample'));

        $this->renderLayout();
        
    }

    public function addAction() {
        $this->_title($this->__('New Theme'));
        
        $_model  = Mage::getModel('ves_tempcp/theme');
        Mage::register('theme_data', $_model);
        Mage::register('current_theme', $_model);
        
        $this->_initAction();
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Theme Manager'), Mage::helper('adminhtml')->__('Theme Manager'), $this->getUrl('*/*/'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add Theme'), Mage::helper('adminhtml')->__('Add Theme'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }

        $this->_addContent($this->getLayout()->createBlock('ves_tempcp/adminhtml_theme_add'));
        
        $this->renderLayout();
        
    }

    public function editAction() {
        $themeId     = $this->getRequest()->getParam('id');

        $backup     = $this->getRequest()->getParam('backup');

        $backupsetting     = $this->getRequest()->getParam('backupsetting');

        $theme = Mage::helper('ves_tempcp/theme');
        $theme->getTheme( $themeId );
        
        if ( $theme->theme_id ) {

            $this->_title( $this->__('Venus Theme Control Panel - Edit '.$theme->group ));
            
            $this->_initAction();
            /*Export sample data of the current theme*/
            if($backup) {
                $theme_name = $theme->group;
                $stores = $theme->get("store_id", array());
                Mage::register('current_stores', $stores);
                Mage::register('theme_name', $theme_name);
                Mage::helper("ves_tempcp/exportSample")->export( $theme_name, "full", $stores );

               $this->_redirect('*/adminhtml_theme/edit', array("id"=>$themeId));

            } elseif($backupsetting) {
                $theme_name = $theme->group;
                $stores = $theme->get("store_id", array());
                Mage::register('current_stores', $stores);
                Mage::register('theme_name', $theme_name);
                Mage::helper("ves_tempcp/exportSample")->export( $theme_name, "setting", $stores );

               $this->_redirect('*/adminhtml_theme/edit', array("id"=>$themeId));
            }

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Ves Theme Manager'), Mage::helper('adminhtml')->__('Ves Theme Manager'), $this->getUrl('*/*/'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Theme'), Mage::helper('adminhtml')->__('Edit Theme'));
            
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
                $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
            }
            if ($head = $this->getLayout()->getBlock('head')) {
                $head->addItem('js', 'prototype/window.js')
                    ->addItem('js_css', 'prototype/windows/themes/default.css')
                    ->addCss('lib/prototype/windows/themes/magento.css')
                    ->addItem('js', 'mage/adminhtml/variables.js')
                    ->addItem('js', 'mage/adminhtml/wysiwyg/widget.js')
                    ->addItem('js', 'lib/flex.js')
                    ->addItem('js', 'lib/FABridge.js')
                    ->addItem('js', 'mage/adminhtml/flexuploader.js')
                    ->addItem('js', 'mage/adminhtml/browser.js')
                    ;
            }
            $this->_addContent($this->getLayout()->createBlock('ves_tempcp/adminhtml_theme_edit'));
            
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ves_tempcp')->__('The theme does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function customizeAction() {
        $themeId     = $this->getRequest()->getParam('id');

        $theme = Mage::helper('ves_tempcp/theme');
        $theme->getTheme( $themeId );
        
        if ( $theme->theme_id ) {

            $this->_title( $this->__('Venus Theme Control Panel - Live Customize Theme '.$theme->title ));
            
            $this->_initAction();
            
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            if ($head = $this->getLayout()->getBlock('head')) {
                $head->addItem('js', 'prototype/window.js')
                    ->addItem('js_css', 'prototype/windows/themes/default.css')
                    ->addCss('lib/prototype/windows/themes/magento.css')
                    ->addItem('js', 'mage/adminhtml/variables.js')
                    ->addItem('js', 'mage/adminhtml/wysiwyg/widget.js')
                    ->addItem('js', 'lib/flex.js')
                    ->addItem('js', 'lib/FABridge.js')
                    ->addItem('js', 'mage/adminhtml/flexuploader.js')
                    ->addItem('js', 'mage/adminhtml/browser.js')
                    ;
            }

            $customize_block = $this->getLayout()->createBlock('ves_tempcp/adminhtml_theme_customize');

            $this->getResponse()->setBody( $customize_block->toHtml() );

            return;
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ves_tempcp')->__('The theme does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function saveCustomizeAction() {
        if ($data = $this->getRequest()->getPost()) {
            $selectors = $data['customize'];
            $matches = $data["customize_match"];
            $id = (int)$this->getRequest()->getParam('id');
            $theme_model = Mage::getModel('ves_tempcp/theme')->load($id);
            $theme = $theme_model->getGroup();
            $tmp_theme = explode("/", $theme);
            if(count($tmp_theme) == 1) {
                $theme = "default/".$theme;
            }
            $output = '';
            $cache = array();
            $themeCustomizePath = Mage::helper("ves_tempcp")->getThemeCustomizePath( $theme );

            foreach( $selectors as $match => $customizes  ){
                $output .= "\r\n/* customize for $match */ \r\n";
                foreach( $customizes as $key => $customize ){
                    if( isset($matches[$match]) && isset($matches[$match][$key]) ){
                        $tmp = explode("|", $matches[$match][$key]);

                        if( trim($customize) ) {
                            $output .= $tmp[0]." { ";
                            if( strtolower(trim($tmp[1])) == 'background-image'){
                                $output .= $tmp[1] . ':url('.$customize .')!important';
                            }elseif( strtolower(trim($tmp[1])) == 'font-size' ){
                                $output .= $tmp[1] . ':'.$customize.'px!important';   
                            }else {
                                $output .= $tmp[1] . ':#'.$customize.'!important';   
                            }
                            
                            $output .= "} \r\n";
                        }
                        $cache[$match][] =  array('val'=>$customize,'selector'=>$tmp[0] );
                    }
                }   

            }
             
            if(  !empty($data['saved_file'])  ){
                if( $data['saved_file'] && file_exists($themeCustomizePath.$data['saved_file'].'.css') ){
                    unlink( $themeCustomizePath.$data['saved_file'].'.css' );
                }
                if( $data['saved_file'] && file_exists($themeCustomizePath.$data['saved_file'].'.json') ){
                    unlink( $themeCustomizePath.$data['saved_file'].'.json' );
                }
                $nameFile = $data['saved_file'];
            }else {
                if( isset($data['newfile']) && empty($data['newfile']) ){
                    $nameFile = time();
                }else {
                    $nameFile = preg_replace("#\s+#", "-", trim($data['newfile']));
                }
            }
        
            if( $data['action-mode'] != 'save-delete' ){
                
                if( !empty($output) ){
                    Mage::helper("ves_tempcp")->writeToCache( $themeCustomizePath, $nameFile, $output );
                }
                if( !empty($cache) ){
                    Mage::helper("ves_tempcp")->writeToCache(  $themeCustomizePath, $nameFile, json_encode($cache),"json" );
                }

             }  
              
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('Theme Profile Was Saved Successfully.'));
            
            $this->_redirect('*/adminhtml_theme/customize', array("id"=>$id));
        }
    }

    public function setdefaultAction(){
       $this->updateStatus(1);
    }

    public function removedefaultAction(){
       $this->updateStatus(0);
    }

    public function ajaxsaveAction(){
        
    }

    public function ajaxuploadAction(){
        $result = array();
        $success = 0;
        $result['success'] = 0;
        if(isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {  
                try {

                    

                    /* Starting upload */   
                    $uploader = new Varien_File_Uploader('file');
                    
                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    
                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //  (file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);
                    
                    /*If upload logo image*/
                    if(1 == $this->getRequest()->getParam("islogo")){
                        $id = (int)$this->getRequest()->getParam("id");
                        $theme_model = Mage::getModel('ves_tempcp/theme')->load($id);
                        $theme = $theme_model->getGroup();
                        $tmp_theme = explode("/", $theme);
                        if(count($tmp_theme) == 1) {
                            $theme = "default/".$theme;
                        }

                        $path = Mage::getBaseDir('skin')."/frontend/".$theme.'/images';
                        $uploader->save($path, $_FILES['file']['name'] );
                        $result['success'] = 1;
                        //this way the name is saved in DB
                        $result['file'] = 'images/' . $_FILES['file']['name'];
                    } else {
                        // We set media as the upload dir
                        $path = Mage::getBaseDir('media') . '/ves_tempcp/upload/';
                        $uploader->save($path, $_FILES['file']['name'] );
                        $result['success'] = 1;
                        //this way the name is saved in DB
                        $result['file'] = 'ves_tempcp/upload/' . $_FILES['file']['name'];
                    }
                    

                    $success = 1;
                    
                } catch (Exception $e) {   
                    $result = array('success' => 0, 'error'=>$e->getMessage(), 'errorcode'=>$e->getCode());
                }
        }
        echo $success;
        exit;
        //$this->getResponse()->setBody(Zend_Json::encode($result));
    }
    public function ajaxdeletefileAction() {
        $json = array();
        $json['success'] = 0;
        if ($data = $this->getRequest()->getPost()) {
            $filetype = isset($data["filetype"])?$data["filetype"]:"";
            $filename = isset($data["filename"])?$data["filename"]:"";
            $theme_id = $this->getRequest()->getParam('id');
            if($filename && $theme_id) {
                $_model = Mage::getModel('ves_tempcp/theme')->load($theme_id);

                $theme_group = $_model->getGroup();
                $tmp_theme = explode("/", $theme_group);
                if(count($tmp_theme) == 1) {
                    $theme_group = "default/".$theme_group;
                }
                $file_path  = "";
                $file_name = "";

                if($filetype == "custom_css") {
                    $file_path = Mage::getBaseDir('skin')."/frontend/".$theme_group."/css/local/";
                    $file_name = $filename.".css";
                } elseif($filetype == "custom_js") {
                    $file_path = Mage::getBaseDir('skin')."/frontend/".$theme_group."/js/";
                    $file_name = $filename.".js";
                }

                if($file_path && file_exists($file_path)) {
                    $flocal = new Varien_Io_File();
                    $flocal->open(array('path' => $file_path));
                    $flocal->rm($file_name);
                    $flocal->close();
                    $json['success'] = 1;
                }
            }
            
        }
        echo Mage::helper('core')->jsonEncode( $json );
    }
    public function ajaxloadfileAction() {
        $json = array();
        if ($data = $this->getRequest()->getPost()) {
            $filetype = isset($data["filetype"])?$data["filetype"]:"";
            $filename = isset($data["filename"])?$data["filename"]:"";
            $theme_id = $this->getRequest()->getParam('id');
            if($filename && $theme_id) {
                $_model = Mage::getModel('ves_tempcp/theme')->load($theme_id);

                $theme_group = $_model->getGroup();
                $tmp_theme = explode("/", $theme_group);
                if(count($tmp_theme) == 1) {
                    $theme_group = "default/".$theme_group;
                }
                $file_path  = "";
                if($filetype == "custom_css") {
                    $file_path = Mage::getBaseDir('skin')."/frontend/".$theme_group."/css/local/".$filename.".css";
                } elseif($filetype == "custom_js") {
                    $file_path = Mage::getBaseDir('skin')."/frontend/".$theme_group."/js/".$filename.".js";
                }

                if($file_path && file_exists($file_path)) {
                    $json['content'] = file_get_contents($file_path);
                }
            }
            
        }
        echo Mage::helper('core')->jsonEncode( $json );
    }
    public function backupAssetsAction() {
        $json = array();
        if ($data = $this->getRequest()->getPost()) {
            $backup_type = $this->getRequest()->getParam("backup");
            $content = isset($data['content'])?$data['content']:'';
            $theme_id = $this->getRequest()->getParam('id');
            $_model = Mage::getModel('ves_tempcp/theme')->load($theme_id);
            $file_path = "";
            $file_name = "";
            $ext = "";
            if(!empty($content) && $backup_type == "css") {
                $theme_group = $_model->getGroup();
                $tmp_theme = explode("/", $theme_group);
                if(count($tmp_theme) == 1) {
                    $theme_group = "default/".$theme_group;
                }
                $file_path = Mage::getBaseDir('skin')."/frontend/".$theme_group."/css/local/";
                $file_name = "backup_css_".time();
                $ext = "css";

            } elseif(!empty($content) && $backup_type == "js") {
                $theme_group = $_model->getGroup();
                $tmp_theme = explode("/", $theme_group);
                if(count($tmp_theme) == 1) {
                    $theme_group = "default/".$theme_group;
                }
                $file_path = Mage::getBaseDir('skin')."/frontend/".$theme_group."/js/";
                $file_name = "backup_js_".time();
                $ext = "js";
            }
            if(!empty($content) && $file_path ) {

                if(!file_exists($file_path)) {
                    $file = new Varien_Io_File();
                    $file->mkdir($file_path);
                    $file->close();
                }

                Mage::helper("ves_tempcp")->writeToCache( $file_path, $file_name, $content , $ext );

                $json['sussess'] = 1;
            } else {
                $json['sussess'] = 0;
            }
        } else {
            $json['sussess'] = 0;
        }
        echo Mage::helper('core')->jsonEncode( $json );
    }
    /*Save Action*/
    public function saveAction() {
        $action = "";
        if ($data = $this->getRequest()->getPost()) {
            $action = $this->getRequest()->getParam('action');
            $themecontrol = isset($data['themecontrol'])?$data['themecontrol']:'';
            $internal_modules = isset($data['module'])?$data['module']:array();

            if(isset($_FILES['bg_image']['name']) && $_FILES['bg_image']['name'] != '') {       
                    try {   
                        /* Starting upload */   
                        $uploader = new Varien_File_Uploader('bg_image');
                        
                        // Any extention would work
                        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                        $uploader->setAllowRenameFiles(false);
                        
                        // Set the file upload mode 
                        // false -> get the file directly in the specified folder
                        // true -> get the file in the product like folders 
                        //  (file.jpg will go in something like /media/f/i/file.jpg)
                        $uploader->setFilesDispersion(false);
                                
                        // We set media as the upload dir
                        $path = Mage::getBaseDir('media') . '/ves_tempcp/upload/';
                        $uploader->save($path, $_FILES['bg_image']['name'] );
                        
                    } catch (Exception $e) {
                        
                    }
                    //this way the name is saved in DB
                    $themecontrol['bg_image'] = 'ves_tempcp/upload/' . $_FILES['bg_image']['name'];
            } elseif(isset($themecontrol['bg_image']) && strpos($themecontrol['bg_image'], "ves_tempcp/upload/") === false) {
                $themecontrol['bg_image'] = "ves_tempcp/upload/" . $themecontrol['bg_image'];
            } elseif(isset($data['delete_bg_image']) && $data['delete_bg_image']) {
                if(file_exists(Mage::getBaseDir('media') . "/".$themecontrol['bg_image'])) {
                    @unlink(Mage::getBaseDir('media') . "/".$themecontrol['bg_image']);
                }
                $themecontrol['bg_image'] = "";

            }

            if(isset($themecontrol['custom_logo']) && strpos($themecontrol['custom_logo'], "images/") === false) {
                $themecontrol['custom_logo'] = "images/".$themecontrol['custom_logo'];
            } elseif(isset($data['delete_custom_logo']) && $data['delete_custom_logo']) {
                if(file_exists(Mage::getBaseDir('skin')."/frontend/".$theme_group."/images/".$themecontrol['custom_logo'])) {
                    @unlink(Mage::getBaseDir('skin')."/frontend/".$theme_group."/images/".$themecontrol['custom_logo']);
                }
                $themecontrol['custom_logo'] = "";

            }

             if(isset($themecontrol['custom_logo_small']) && strpos($themecontrol['custom_logo_small'], "images/") === false) {
                $themecontrol['custom_logo_small'] = "images/".$themecontrol['custom_logo_small'];
            } elseif(isset($data['delete_custom_logo_small']) && $data['delete_custom_logo_small']) {
                if(file_exists(Mage::getBaseDir('skin')."/frontend/".$theme_group."/images/".$themecontrol['custom_logo_small'])) {
                    @unlink(Mage::getBaseDir('skin')."/frontend/".$theme_group."/images/".$themecontrol['custom_logo_small']);
                }
                $themecontrol['custom_logo_small'] = "";

            }

            $data = array();
            $custom_css = "";
            if(isset($themecontrol['custom_css'])) { 
                $custom_css = trim($themecontrol['custom_css']);
                unset($themecontrol['custom_css']);
            }
            $theme_id = $this->getRequest()->getParam('id');
            $data['params'] = base64_encode(serialize($themecontrol));
            $data['group'] = isset($themecontrol['default_theme'])?$themecontrol['default_theme']:'ves default theme';
            $data['is_default'] = 1;
            $data['stores'] = $this->getRequest()->getParam('stores');
            $_model = Mage::getModel('ves_tempcp/theme')->load($theme_id);

            /*
            if(empty($data['theme_id']) && $_model->checkExistsByGroup($data['group'])){

                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ves_tempcp')->__('The Theme is exist, you can not create a same theme!'));
            */
            //    $this->_redirect('*/*/');
            /*    return;

            }*/

            $_model->setData($data);

            if($theme_id)
                $_model->setId($theme_id);
            try {

                $_model->save();


                /*Save custom css*/
                
                if(!empty($custom_css)) {
                    $theme_group = $_model->getGroup();
                    $tmp_theme = explode("/", $theme_group);
                    if(count($tmp_theme) == 1) {
                        $theme_group = "default/".$theme_group;
                    }
                    $custom_css_path = Mage::getBaseDir('skin')."/frontend/".$theme_group."/css/local/";
                    if(!file_exists($custom_css_path)) {
                        $file = new Varien_Io_File();
                        $file->mkdir($custom_css_path);
                        $file->close();
                    }

                    Mage::helper("ves_tempcp")->writeToCache( $custom_css_path, "custom", $custom_css );
                }
                
                /*End save custom css*/

               

                /*Save internal modules*/
                $theme_id = $_model->getId();
                Mage::getModel('ves_tempcp/module')->cleanModules($theme_id);
                if(!empty($internal_modules)) {
                    foreach($internal_modules as $position => $modules) {
                        if($modules) {
                            foreach($modules as $key=>$module) {
                                
                                $_module_model = Mage::getModel('ves_tempcp/module');

                                $_data = array();
                                $_data['theme_id'] = $theme_id;
                                $_data['module_name'] = trim($key);
                                $_data['module_title'] = trim($module['module_title']);
                                $_data['module_data'] = $module['module_data'];
                                $_data['block_id'] = $module['block_id'];
                                $_data['layout'] = implode(",", $module['layout'] );
                                $_data['status'] = $module['status'];
                                $_data['sort_order'] = $module['sort_order'];
                                $_data['position'] = isset($module['position'])?trim($module['position']):trim($position);

                                $_module_model->setData( $_data );
                                if( $module_id = $_module_model->getModuleId($key) ){
                                    $_module_model->setId($module_id);
                                }

                                try {

                                    $_module_model->save();

                                } catch (Exception $e) {
                                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                                    Mage::getSingleton('adminhtml/session')->setFormData($_data);
                                }
                                
                            }
                        }
                    }
                }
                /*End Save internal modules*/

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ves_tempcp')->__('Theme was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return;
                }
                if($action == "save_stay"){
                    $this->_redirect('*/*/edit', array('id' => $theme_id));
                }else{
                    $this->_redirect('*/*/');
                }
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ves_tempcp')->__('Unable to find theme to save'));
        $this->_redirect('*/*/');
        
    }

    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('ves_tempcp/theme');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Theme was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massSetdefaultAction(){
       $this->massUpdateStatus(1);
    }

     public function massRemovedefaultAction(){
       $this->massUpdateStatus(0);
    }

    public function massDeleteAction() {
        $IDList = $this->getRequest()->getParam('theme');
        if(!is_array($IDList)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select theme(s)'));
        } else {
            try {
                foreach ($IDList as $itemId) {
                    $_model = Mage::getModel('ves_tempcp/theme')
                            ->setIsMassDelete(true)->load($itemId);
                    $_model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($IDList)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massCloneAction() {
        $IDList = $this->getRequest()->getParam('theme');
        if(!is_array($IDList)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select theme(s)'));
        } else {
            try {
                foreach ($IDList as $itemId) {
                    $_model = Mage::getSingleton('ves_tempcp/theme')
                            ->load($itemId);
                    $_data = $_model->getData();
                    $_data['stores'] = $_model->getStoreId();
                    unset($_data['theme_id']);
                    $_clone_model = Mage::getSingleton('ves_tempcp/theme');
                    $_clone_model->setData($_data);
                    $_clone_model->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully clone', count($IDList))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function massUpdateStatus( $status = 0){
        $IDList = $this->getRequest()->getParam('theme');
        if(!is_array($IDList)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select theme(s)'));
        } else {
            try {
                foreach ($IDList as $itemId) {
                    $_model = Mage::getModel('ves_tempcp/theme')->load($itemId);
                    $stores = $_model->getStoreId();
                    $_model->setIsDefault( $status ) ;
                    $_model->setStores($stores);
                    $_model->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully changed Default', count($IDList)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function updateStatus($status = 0){
        $themeId     = $this->getRequest()->getParam('id');

        $_model = Mage::getModel('ves_tempcp/theme')
                ->load($themeId);
        $stores = $_model->getStoreId();        
        $_model->setIsDefault( $status ) ;
        $_model->setStores($stores);

        if ( $_model->save() ) {
            $theme = $_model->getGroup();
            
            $tmp_theme = explode("/", $theme);
            $package = "default";
            if(count($tmp_theme) > 1) {
                $package = $tmp_theme[0];
                $theme = $tmp_theme[1];
            }
            if($stores) {
                foreach($stores as $key=>$store_id) {
                    if($store_id) {
                        Mage::getConfig()->saveConfig('design/package/name', $package, 'stores', $store_id );
                        Mage::getConfig()->saveConfig('design/theme/default', $theme, 'stores', $store_id );
                    } else {
                        Mage::getConfig()->saveConfig('design/package/name', $package );
                        Mage::getConfig()->saveConfig('design/theme/default', $theme );
                    }
                }
            } else {
                Mage::getConfig()->saveConfig('design/package/name', $package );
                Mage::getConfig()->saveConfig('design/theme/default', $theme );
            }
            

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ves_tempcp')->__('Changed Is Default theme successfully.'));
            $this->_redirect('*/*/');
        }else{
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ves_tempcp')->__('The theme can not save.'));
            $this->_redirect('*/*/');
        }
    }

   
    /**
     * Handles CSV upload
     * @return string $filepath
     */
    protected function getCsvFile($file = "static_blocks.csv", $theme = "") {
        $filepath = null;
        $path = Mage::helper('ves_tempcp/data')->getImportCMSPath($theme);
        $filepath = $path . $file;
        
        return $filepath;

    }
    
    public function cleancssjscacheAction() {
        $id = (int)$this->getRequest()->getParam('id');
        $theme_model = Mage::getModel('ves_tempcp/theme')->load($id);
        $theme = $theme_model->getGroup();
        $tmp_theme = explode("/", $theme);
        if(count($tmp_theme) == 1) {
            $theme = "default/".$theme;
        }
        $response = array();
        if($id && $theme ) {
            $cacheDir = Mage::getBaseDir('skin').'/frontend/'.$theme."/cache/";
  
            if(file_exists($cacheDir)) {
                $pcache = new VesTempcp_Cache();
                $pcache->delete("css", $cacheDir);
                $pcache->delete("js", $cacheDir);

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('Cleaned up js,css cache files.'));
                $response['success'] = Mage::helper('cms')->__('Cleaned up js,css cache files.');
            }
           
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('Can not clean up js,css cache files.'));
            $response['error'] = Mage::helper('cms')->__('Can not clean up js,css cache files.');
            
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }


    /**
     * Handles CSV upload
     * @return string $filepath
     */
    private function getUploadedFile() {
        $filepath = null;

        if(isset($_FILES['importfile']['name']) and (file_exists($_FILES['importfile']['tmp_name']))) {
            try {
                $uploader = new Varien_File_Uploader('importfile');
                $uploader->setAllowedExtensions(array('csv','txt')); // or pdf or anything
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);

                $path = Mage::helper('ves_tempcp/data')->getImportPath2();
                $uploader->save($path, $this->destination_filename);
                $filepath = $path . $this->destination_filename;

            } catch(Exception $e) {
                // log error
                Mage::logException($e);
            } // end if

        } // end if

        return $filepath;

    }
    //Start Import/ Install Sample Data
     public function storesampleAction($is_redirect = true) {

        $id = (int)$this->getRequest()->getParam('id');
        $theme_model = Mage::getModel('ves_tempcp/theme')->load($id);
        $theme = $theme_model->getGroup();
        $tmp_theme = explode("/", $theme);
        $package = "default";
        if(count($tmp_theme) > 1) {
            $package = $tmp_theme[0];
            $theme = $tmp_theme[1];
        }

        if($id && $theme) {
            $filepath = Mage::helper("ves_tempcp")->getImportPath($package."/".$theme).$this->cms_home_page;

            $web_settings = array();
            if(file_exists($filepath)) {
                $content = file_get_contents($filepath);
                $web_settings = Mage::helper('core')->jsonDecode($content);
            }
            $stores = $theme_model->getStoreId( );

            if($stores) {
                foreach($stores as $key=>$store_id) {
                    if($store_id) {
                        Mage::getConfig()->saveConfig('design/package/name', $package, 'stores', $store_id );
                        Mage::getConfig()->saveConfig('design/theme/default', $theme, 'stores', $store_id );

                        if($web_settings && isset($web_settings['config'])) {
                            if(is_array($web_settings['config'])) {
                                foreach($web_settings['config'] as $key2 => $val) {
                                    Mage::getConfig()->saveConfig('web/default/cms_home_page', $val, 'stores', $store_id );
                                }
                                
                            }else {
                                Mage::getConfig()->saveConfig('web/default/cms_home_page', $web_settings['config'], 'stores', $store_id );
                            }
                        }
                    } else {
                        Mage::getConfig()->saveConfig('design/package/name', $package );
                        Mage::getConfig()->saveConfig('design/theme/default', $theme );

                        if($web_settings && isset($web_settings['config'])) {
                            if(is_array($web_settings['config'])) {
                                foreach($web_settings['config'] as $key2 => $val) {
                                    Mage::getConfig()->saveConfig('web/default/cms_home_page', $val, 'stores', $key2 );
                                }
                                
                            }else {
                                Mage::getConfig()->saveConfig('web/default/cms_home_page', $web_settings['config']);
                            }
                        }
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('The theme %s was set to default theme of store successfully.', $theme));
            }
            else {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('The theme dont have any stores to set config default theme .'));
            }
           
        } else {
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('The theme dont existed, fail to set it to default theme.'));
            
        }
        if($is_redirect) {
            $this->_redirect('*/adminhtml_theme/edit', array("id"=>$id));
        }
       
    }

    public function restoreSettingAction( $theme = "") {
        if(!$theme) {
            $id = (int)$this->getRequest()->getParam('id');
            $theme_model = Mage::getModel('ves_tempcp/theme')->load($id);
            $theme = $theme_model->getGroup();
            $tmp_theme = explode("/", $theme);
            if(count($tmp_theme) == 1) {
                $theme = "default/".$theme;
            }
        }
        $module = $this->getRequest()->getParam('module');
        $type = $this->getRequest()->getParam('type');

        if($id && $theme && $module) {
            $importDir = Mage::getBaseDir('cache') ."/backup_".str_replace( "/", "_", $theme).'/'.$module.".".$type;
            if(file_exists($importDir)) {
                $content = file_get_contents($importDir);
                $module = strtolower($module);
                Mage::helper("ves_tempcp/importSample")->importSample( $content, $module, $type);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('Restored config for the module "%s".', $module));
            }
           
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('Can not restore config for the module "%s".', $module));
            
        }

        $this->_redirect('*/adminhtml_theme/edit', array("id"=>$id));
    }
    public function installSampleAction( $params = array(), $is_redirect = true) {
        $id = (int)$this->getRequest()->getParam('id');
        //Override theme id
        if($params && isset($params['id']) && $params['id']) {
            $id = $params['id'];
        }
        $theme_model = Mage::getModel('ves_tempcp/theme')->load($id);
        $theme = $theme_model->getGroup();
        $tmp_theme = explode("/", $theme);
        if(count($tmp_theme) == 1) {
            $theme = "default/".$theme;
        }
        $file_type = $this->getRequest()->getParam('filetype');
        $file_type = $file_type?$file_type:"json";
        $module = $this->getRequest()->getParam('module');
        $type = $this->getRequest()->getParam('type');
        //Override params
        if($params) {
            $file_type = (isset($params['file_type']) && $params['file_type'] )?$params['file_type']:$file_type;
            $module = (isset($params['module']) && $params['module'] )?$params['module']:$module;
            $type = (isset($params['type']) && $params['type'] )?$params['type']:$type;
        }

        $is_override = false;
        if($type == "query") {
           $is_override = false;
        } elseif($type == "override") {
            $is_override = true;
        }

        if($id && $theme ) {
            $importDir = Mage::helper("ves_tempcp")->getImportPath( $theme )."modules/";
            if(!Mage::registry("current_theme")) {
                Mage::register("current_theme", $theme_model);
            }
            
            if($module != "Ves_Tempcp" && file_exists($importDir.$module.".".$file_type) && Mage::helper("ves_tempcp/exportSample")->checkModuleInstalled($module)) {
                $content = file_get_contents($importDir.$module.".".$file_type);
                $module = strtolower($module);
                Mage::helper("ves_tempcp/importSample")->importSample( $content, $module, $file_type, $is_override);

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('Successfully import sample data for the module "%s".', $module));
            }
               
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('Can not import sample data for the module "%s".', $module));
            
        }
        if($is_redirect) {
            $this->_redirect('*/adminhtml_theme/edit', array("id"=>$id));
        }
        
    }

    public function uploadCsvAction() {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('ves_tempcp/adminhtml_cms_enhanced_theme_upload');
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

    public function importCsvAction( $params = array(), $is_redirect = true) {

        // get uploaded file
        $filepath = $this->getUploadedFile();
        $default_theme = $this->getRequest()->getParam('default_theme');
        /*Override param default theme*/
        if($params ) {
            $default_theme = 1;
        }

        $stores = Mage::helper("ves_tempcp")->getAllStores();

        if( !$filepath ) {
            $id = (int)$this->getRequest()->getParam('id');
            /*Override param theme id*/
            if($params && isset($params['id']) && $params['id']) {
                $id = $params['id'];
            }
            $_model = Mage::getModel('ves_tempcp/theme')->load($id);
            $theme = $_model->getGroup();
            $stores = $_model->getStoreId();

            $tmp_theme = explode("/", $theme);
            if(count($tmp_theme) == 1) {
                $theme = "default/".$theme;
            }
           // $stores = $_model->getStoreId();

            $filepath = Mage::helper("ves_tempcp")->getImportPath($theme).$this->import_ves_tempcp;
            if(!file_exists($filepath)) {
                $filepath = null;
            }
            
        }

        if ($filepath != null) {
            try {
                // import into model
                Mage::getSingleton('ves_tempcp/import_theme')->process($filepath, $stores);

                if($default_theme == "1") {
                    $this->storesampleAction(false);
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('CSV Imported Successfully'));
                } else {
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('CSV Imported Successfully'));
                }
               

            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('An Error occured importing CSV.'));
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } // end if
        }
        if($is_redirect) { 
            // redirect to grid page.
            $this->_redirect('*/*/index');
        }
    }
   

    public function importAction($params = array(), $is_redirect = true){
        // get uploaded file
        $type = $this->getRequest()->getParam('type');
        $id = $this->getRequest()->getParam('id');
        /*Override param*/
        if(!empty($params)) {
            $type = (isset($params['type']) && !empty($params['type']))?$params['type']:$type;
            $id = (isset($params['id']) && !empty($params['id']))?$params['id']:$id;
        }
        $theme_model = Mage::getModel('ves_tempcp/theme')->load($id);
        $theme = $theme_model->getGroup();
        $stores = $theme_model->getStoreId();
        if(empty($stores)) {
            $stores = Mage::helper("ves_tempcp")->getAllStores();
        }
        $tmp_theme = explode("/", $theme);
        if(count($tmp_theme) == 1) {
            $theme = "default/".$theme;
        }
        $file = "";
        $import_type = "static_block";
        switch ($type) {
            case 'staticblock':
            case 'static_block':
            case 'static_blocks':
                $file = $this->import_static_blocks;
                $filepath = $this->getCsvFile($file, $theme);
                break;
            case 'pages':
                $file = $this->import_cms_pages;
                $import_type = "cms_page";
                $filepath = $this->getCsvFile($file, $theme);
                break;
            case 'stores':
                $import_type = "store";
                $filepath = Mage::helper("ves_tempcp")->getImportPath($theme).$this->import_stores;
                if(!file_exists($filepath)) {
                    $filepath = null;
                }
                break;
            default:
                break;
        }

        if ($filepath != null) {
            try {
                // import into model

                
                if($import_type == "cms_page"){
                    Mage::getSingleton('ves_tempcp/import_page')->process($filepath, $stores);

                    $resource = Mage::getSingleton('core/resource');
                    /**
                        * Retrieve the write connection
                        */
                    $writeConnection = $resource->getConnection('core_write');
                    $cms_page_store_table = $resource->getTableName("cms/page_store");
                    $core_store = $resource->getTableName("core/store");
                    $writeConnection->query("DELETE FROM `".$cms_page_store_table."` WHERE store_id NOT IN (SELECT store_id FROM `".$core_store."`)");
                    
                }else if($import_type == "static_block"){
                    Mage::getSingleton('ves_tempcp/import_block')->process($filepath, $stores);
                }else if($import_type == "store") {
                    Mage::getSingleton('ves_tempcp/import_store')->process($filepath, $stores);
                }
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('CSV Imported Successfully'));

            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('An Error occured importing CSV.'));
            }
        } // end
        // redirect to grid page.
        if($is_redirect) {
            $this->_redirect('*/adminhtml_theme/edit', array("id"=>$id));
        }
        
    }

    public function fixCmsPageAction() {
        $id = $this->getRequest()->getParam('id');
        try {
            $resource = Mage::getSingleton('core/resource');
            /**
                * Retrieve the write connection
                */
            $writeConnection = $resource->getConnection('core_write');
            $cms_page_store_table = $resource->getTableName("cms/page_store");
            $core_store = $resource->getTableName("core/store");
            $writeConnection->query("DELETE FROM `".$cms_page_store_table."` WHERE store_id NOT IN (SELECT store_id FROM `".$core_store."`)");

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('404 not found CMS page was fixed successfully!'));

        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('An Error occured fix 404 not found.'));
        }
        // redirect to grid page.
        $this->_redirect('*/adminhtml_theme/edit', array("id"=>$id));
    }

    public function createTempTheme($group = "ves default theme", $store_id = 0) {
        $theme_id = 0;
        $data['params'] = '';
        $data['group'] = $group;
        $data['is_default'] = 1;
        $data['stores'] = $store_id;
        $_model = Mage::getModel('ves_tempcp/theme')->load($theme_id);

        $_model->setData($data);

        try {
            $_model->save();
            $_GET['default_theme'] = 1;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            return false;
        }

        return $_model->getId();
    }
    public function massInstallAction() {
        //get Id theme
        $theme_group = "";
        $temp_theme_id = 0;
        if( $data = $this->getRequest()->getPost() ) {
            $theme_group = isset($data['theme_group'])?$data['theme_group']:'';
            $store_id = isset($data['stores'])?$data['stores']:0;
            $temp_theme_id = $id = $this->createTempTheme( $theme_group, $store_id );
            $_GET['id'] = $id;
            
            if(!$id) {
                Mage::getSingleton('adminhtml/session')->addError($this->__("Error: can not create temp theme before setup data sample"));
                $this->_redirect('*/adminhtml_theme/datasample');
                return;
            }
        } else {
            $id = $this->getRequest()->getParam('id');

            if(!$id) {
                Mage::getSingleton('adminhtml/session')->addError($this->__("Error: Theme ID are missing. Can not setup data sample without theme ID."));
                $this->_redirect('*/adminhtml_theme/datasample');
                return;
            }
        }

        
        $themecontrol = Mage::helper('ves_tempcp/theme');
        $themecontrol->getTheme( $id );
                       
        if($themecontrol->theme_id) {

            $themecontrol->initTheme();

            $modulesQuery = $themecontrol->get("modules_query", array());
            $samples = $themecontrol->get("samples", array());
            $current_stores = $themecontrol->get("store_id", array(0));

            Mage::register("current_stores", $current_stores);
            //Install Store Views
            /*
            $store_params = array("type"=>"stores", "id" => $id);
            $this->importAction($store_params, false);*/

            //Install Theme Settings + Set default design package/template 
            $theme_params = array("default_theme"=>"1", "id" => $id);
            $this->importCsvAction($theme_params, false);
            
            //Install CMS Pages/Blocks Sample Data
            $block_params = array("type"=>"static_blocks", "id" => $id);
            $this->importAction($block_params, false);


            $page_params = array("type"=>"pages", "id" => $id);
            $this->importAction($page_params, false);

            //Install Theme's Modules Sample Data
            if($modulesQuery) {
                //Install modules sample query it will delete all exists data and insert new data
                foreach($modulesQuery as $key=>$val) {
                    if(strtolower($key) != "ves_tempcp") {
                        $params = array("id" => $id, 
                                        "module" => $key,
                                        "type" => "query");
                        $this->installSampleAction( $params, false);
                    }
                }
            }

            if($samples) {
                //Install sample data, it will insert new data or replace exists data
                foreach($samples as $key=>$val) {
                    if(strtolower($key) != "ves_tempcp") {
                        $params = array("id" => $id, 
                                        "module" => $key,
                                        "type" => "override");
                        $this->installSampleAction( $params, false);
                    }
                }
            }
            //Install sample data for CMS Widgets
            $params = array("id" => $id, 
                            "module" => "Mage_Widget",
                            "type" => "override");
            $this->installSampleAction( $params, false);

            /*Delete current theme settings*/
            $_model = Mage::getModel('ves_tempcp/theme')->load($id);
            $_model->delete();
        }
        
        $this->_redirect('*/*/index');
    }
    //Start Export Data
    /**
     * Exports a CSV file
     */
    public function exportAction() {
        $type = $this->getRequest()->getParam('type');
        $id = $this->getRequest()->getParam('id');
        $theme_model = Mage::getModel('ves_tempcp/theme')->load($id);
        $theme = $theme_model->getGroup();
        $tmp_theme = explode("/", $theme);
        if(count($tmp_theme) == 1) {
            $theme = "default/".$theme;
        }
        $fileName = "";
        $content = "";
        switch ($type) {
            case 'staticblock':
            case 'static_block':
            case 'static_blocks':
                $fileName = $this->import_static_blocks;
                $content    = $this->getLayout()->createBlock('ves_tempcp/adminhtml_cms_enhanced_block_grid')->getCsvFile($fileName);

                break;
            
            default:
            case 'pages':
                $fileName = $this->import_cms_pages;
                $content    = $this->getLayout()->createBlock('ves_tempcp/adminhtml_cms_enhanced_page_grid')->getCsvFile($fileName);
                break;
        }

        $this->_prepareDownloadResponse($fileName, $content);

    }

    public function exportCsvAction(){
        $fileName = "ves_themes.csv";
        $content    = $this->getLayout()->createBlock('ves_tempcp/adminhtml_cms_enhanced_theme_grid')->getCsvFile( $fileName );

        $this->_prepareDownloadResponse($fileName, $content);

    }

    protected function _title($text = null, $resetIfExists = true)
    {
        if (is_string($text)) {
            $this->_titles[] = $text;
        } elseif (-1 === $text) {
            if (empty($this->_titles)) {
                $this->_removeDefaultTitle = true;
            } else {
                array_pop($this->_titles);
            }
        } elseif (empty($this->_titles) || $resetIfExists) {
            if (false === $text) {
                $this->_removeDefaultTitle = false;
                $this->_titles = array();
            } elseif (null === $text) {
                $this->_removeDefaultTitle = true;
                $this->_titles = array();
            }
        }
        return $this;
    }
}
<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_FAQ
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Tempcp Extension
 *
 * @category   Ves
 * @package    Ves_Tempcp
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Tempcp_LiveeditorController extends Mage_Core_Controller_Front_Action
{
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
            try {
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
                    Mage::getSingleton('catalog/session')->addSuccess( Mage::helper('ves_tempcp')->__('Saved custom css file successfully!')
                    );
                }
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
            }
            
        }
        $this->_redirectReferer(); 
    }
}
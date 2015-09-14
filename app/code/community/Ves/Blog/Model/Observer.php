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
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Blog Extension
 *
 * @category   Ves
 * @package    Ves_Blog
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Blog_Model_Observer  extends Varien_Object
{

	/**
	 *
	 */
	public function initControllerRouters($observer){
		Mage::getSingleton('core/session', array('name'=>'adminhtml'));
		if (! is_null(Mage::registry("_singleton/admin/session"))) {
			if(Mage::getSingleton('admin/session')->isLoggedIn()){ /*Is admin*/
			  //do stuff
				return;
			}
		}

		$request = $observer->getEvent()->getFront()->getRequest();
		if (!Mage::app()->isInstalled()) {
			/*
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            */
                return;
            }
            $identifier = trim($request->getPathInfo(), '/');

            $condition = new Varien_Object(array(
            	'identifier' => $identifier,
            	'continue'   => true
            	));
            Mage::dispatchEvent('blog_controller_router_match_before', array(
            	'router'    => $this,
            	'condition' => $condition
            	));
            $identifier = $condition->getIdentifier();
            $identifier = trim($identifier, "/");

            if ($condition->getRedirectUrl()) {
            	Mage::app()->getFrontController()->getResponse()
            	->setRedirect($condition->getRedirectUrl())
            	->sendResponse();
            	$request->setDispatched(true);
            	return true;
            }

            if (!$condition->getContinue())
            	return false;

            $route = trim( Mage::getStoreConfig('ves_blog/general_setting/route') );
            if($identifier) {

            	if(  preg_match("#^".$route."(\.html)?$#",$identifier, $match) ) {
            		$request->setModuleName('venusblog')
            		->setControllerName('index')
            		->setActionName('index');
            		$request->setAlias(
            			Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            			$identifier
            			);
            		return true;


            	} elseif(str_replace('/rss', '', str_replace($route, '', $identifier)) == '') {

            		$request->setModuleName('venusblog')
            		->setControllerName('rss')
            		->setActionName('index');

            		$request->setAlias(
            			Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            			$identifier
            			);
            		return true;
            	} elseif( preg_match("#".$route."/tag/(\w+)\.?#",$identifier, $match) ) {

            		if( count($match)<= 1 ){
            			return false;
            		}

            		$request->setModuleName('venusblog')
            		->setControllerName('list')
            		->setActionName('show')
            		->setParam("tag",$match[1]);

            		$request->setAlias(
            			Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            			$identifier
            			);
            		return true;

            	}  elseif( preg_match("#".$route."/archive/(\w+)\.?#",$identifier, $match) ) {

            		if( count($match)<= 1 ){
            			return false;
            		}

            		$request->setModuleName('venusblog')
            		->setControllerName('list')
            		->setActionName('show')
            		->setParam("archive",$match[1]);

            		$request->setAlias(
            			Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            			$identifier
            			);
            		return true;
            	}
            }

            return false;
        }

	/**
	 *
	 */
	public function beforeRender( Varien_Event_Observer $observer ){
		$controller_name = Mage::app()->getRequest()->getControllerModule();
		$menu_name = $controller_name."_".Mage::app()->getRequest()->getControllerName();
		$helper =  Mage::helper('ves_blog/data');


		// if($helper->checkAvaiable( $controller_name )){
			//$config = $helper->get();
		$config = array();
		$this->_loadMedia( $config );
		/**LATEST BLOG */
			//$this->latestModule( $menu_name , $helper );
		/** CATEGORY BLOG */
			//$this->cmenuModule( $menu_name , $helper );
		// }
	}

	public function getModuleConfig( $val ){
		return Mage::getStoreConfig( "ves_blog/module_setting/".$val );
	}

	public function latestModule( $menu_name, $helper ){
		if( !$this->getModuleConfig("enable_latestmodule") ){
			return ;
		}

		if($helper->checkMenuItem( $menu_name, $this->getModuleConfig("latest_menuassignment") )){

			$layout = Mage::getSingleton('core/layout');
			$title = $this->getModuleConfig("latest_title");
			$position = $this->getModuleConfig("latest_position");
			if( !$position ){ $position = "right"; }

			$cposition = $this->getModuleConfig("latest_customposition");
			if( $cposition ){ $position = $cposition; }

			$display = $this->getModuleConfig("latest_display");
			if( $display=="after" ){ $display = true; }else { $display=false; }

			$block =  $layout->createBlock( 'ves_blog/latest' );

			if($myblock = $layout->getBlock( $position )){
				$myblock->insert($block, $title , $display);
			}

		}
	}



	public function cmenuModule( $menu_name, $helper ){
		if( !$this->getModuleConfig("enable_cmenumodule") ){
			return ;
		}
		if($helper->checkMenuItem( $menu_name, $this->getModuleConfig("cmenu_menuassignment") )){

			$layout = Mage::getSingleton('core/layout');
			$title = $this->getModuleConfig("cmenu_title");
			$position = $this->getModuleConfig("cmenu_position");
			if( !$position ){ $position = "right"; }

			$cposition = $this->getModuleConfig("cmenu_customposition");
			if( $cposition ){ $position = $cposition; }

			$display = $this->getModuleConfig("cmenu_display");
			if( $display=="after" ){ $display = true; }else { $display=false; }

			$block =  $layout->createBlock( 'ves_blog/cmenu' );

			if($myblock = $layout->getBlock( $position )){
				$myblock->insert($block, $title , $display);
			}

		}
	}
}

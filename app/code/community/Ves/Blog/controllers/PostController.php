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
class Ves_Blog_PostController extends Mage_Core_Controller_Front_Action
{
	public function preDispatch(){
		if(!Mage::getStoreConfig('ves_blog/general_setting/show')){
			Mage::app()->getFrontController()->getResponse()->setHeader('HTTP/1.1','404 Not Found',true);
			Mage::app()->getFrontController()->getResponse()->setHeader('Status','404 File not found',true);
			$pageId = Mage::getStoreConfig('web/default/cms_no_route');
			$url = rtrim(Mage::getUrl($pageId),'/');
			Mage::app()->getFrontController()->getResponse()->setRedirect($url);
		}
	}

	public function indexAction(){
		$module_name = Mage::app()->getRequest()->getModuleName();
		$this->loadLayout();

		$this->renderLayout();
	 //	Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());
	}
	public function commentAction(){
		if($data = $this->getRequest()->getPost()) {
			$data['store_id'] = Mage::app()->getStore()->getStoreId();
			$check = true;
			if( Mage::getStoreConfig('ves_blog/general_setting/enable_recaptcha') ){
				$recaptcha = Mage::helper('ves_blog/recaptcha')
				->setKeys( Mage::getStoreConfig('ves_blog/general_setting/privatekey'),
					Mage::getStoreConfig("ves_blog/general_setting/publickey") )
				->getReCapcha();
				$check = $recaptcha->verify( $this->getRequest()->getParam('recaptcha_challenge_field'),
					$this->getRequest()->getParam('recaptcha_response_field') )->isValid();
				if( !$check ){
					Mage::getSingleton( 'core/session') ->addError( $this->__("You put wrong captcha, please try again!!!") );
					$this->_redirectUrl( Mage::getModel('ves_blog/post')->load($this->getRequest()->getParam('id'))->getUrl() );
					return ;
				}
			}
			if (!Zend_Validate::is(trim($data['user']) , 'NotEmpty')) { $check = false;	}
			if (!Zend_Validate::is(trim($data['email']) , 'NotEmpty')) { $check = false;	}
			if (!Zend_Validate::is(trim($data['comment']) , 'NotEmpty')) { $check = false;	}


			if( $check ){
				$todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
				try {
					$active = Mage::getStoreConfig('ves_blog/general_setting/comment_publish') ;
					Mage::getModel( 'ves_blog/comment' )->setData( $data )
					->setPostId( $this->getRequest()->getParam('id') )
					->setCreated( $todayDate )
					->setIsActive( $active )
					->save();
					Mage::getSingleton('core/session')->addSuccess( 'Your comment added, it will be published very soon.' );
				} catch (Exception $e) {
					Mage::getSingleton( 'core/session') ->addError( $e->getMessage() );
				}
			}
			//echo '<pre>'.print_r( $data, 1 ); die;
		}

		$this->_redirectUrl( Mage::getModel('ves_blog/post')->load($this->getRequest()->getParam('id'))->getUrl() );
	}
	public function viewAction(){

		$this->loadLayout();

		$this->renderLayout();
	// 	Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());
	}
}
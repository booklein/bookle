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
class Ves_Blog_ListController extends Mage_Core_Controller_Front_Action
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

	public function showAction(){
		$tag = $this->getRequest()->getParam("tag");
		$this->loadLayout();
		$this->renderLayout();
	}
}
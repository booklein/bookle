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
class Ves_Blog_Model_Post extends Mage_Core_Model_Abstract
{
	protected function _construct() {
		$this->_init('ves_blog/post');
	}

	/**
	 *
	 */
	public function getURL(){
		if($isSecure = Mage::app()->getStore()->isCurrentlySecure()) {
			$base_url = Mage::getBaseUrl( Mage_Core_Model_Store::URL_TYPE_WEB, true );
		} else {
			$base_url = Mage::getBaseUrl();
		}
		return $base_url.Mage::getModel('core/url_rewrite')->loadByIdPath('venusblog/post/'.$this->getId())->getRequestPath();
	}

	public function getGeneralConfig( $val ){
		return Mage::getStoreConfig( "ves_blog/general_setting/".$val );
	}

	public function getPostImageSize( $type = "l") {
		$sizes = Mage::helper("ves_blog")->getImageSizeModes();
		$key = "large_imagesize";
		foreach( $sizes as $k => $v ){
			if($v == $type) {
				$key = $k;
				break;
			}
		}

		$c = $this->getGeneralConfig($key, "");
		$tmp = explode( "x", $c );

		$thumb_width = $thumb_height = "";
		if( count($tmp) > 0 && (int)$tmp[0] ){
			$thumb_width = (int)$tmp[0];
			$thumb_height = (int)$tmp[1];
		}

		return array($thumb_width, $thumb_height);

	}
	public function getImageURL( $type = "l" ){
		// Gets the current store's id
		$storeId = Mage::app()->getStore()->getStoreId();
		$image = str_replace("/",DS, $this->getFile());
		$image = str_replace("blog".DS,"", $image);

		$thumb_width = $thumb_height = "";
		$sizes = $this->getPostImageSize( $type );

		$thumb_width = (int)$sizes[0];
		$thumb_height = (int)$sizes[1];


		if($type == "original") {
			return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."blog/".$image;
		} else {
			if(!$storeId){
				if(file_exists(Mage::getBaseDir('media').DS."resized".DS.$type.DS.$this->getFile())) {
					return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."resized/".$type."/".$this->getFile();
				} elseif($thumb_width && $thumb_height) {
					return Mage::helper('ves_blog')->resizeImage($this->getFile(), $type, $thumb_width, $thumb_height);
				}

			}else{
				$imageDir = Mage::getBaseDir('media').DS."resized".DS.$type.DS."blog".DS.$storeId.DS.$image;
				if (file_exists($imageDir)) {
					return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."resized/".$type."/blog/".$storeId."/".$image;
				}else{
					if(file_exists(Mage::getBaseDir('media').DS."resized".DS.$type.DS.$this->getFile())) {
						return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."resized/".$type."/".$this->getFile();
					} elseif($thumb_width && $thumb_height) {
						return Mage::helper('ves_blog')->resizeImage($this->getFile(), $type , $thumb_width, $thumb_height);
					}
				}
			}
		}
		return "";
	}

	public function getCategoryTitle(){
		return Mage::getModel('ves_blog/category')->load($this->getCategoryId())->getTitle();
	}

	public function getCatTitle(){
		return $this->getCategoryTitle();
	}


	public function getCategoryLink(){
		return  Mage::getBaseUrl().Mage::getModel('core/url_rewrite')->loadByIdPath('venusblog/category/'.$this->getCategoryId())->getRequestPath();
	}

	public function getAuthor(){
		$author = Mage::getModel('admin/user')->load($this->getUserId());
		return $author->getFirstname().' '.$author->getLastname();
	}

	public function getAuthorURL(){
		return Mage::getBaseUrl().Mage::getModel('core/url_rewrite')->loadByIdPath('venusblog/list/show/'.$this->getUserId())->getRequestPath();
	}
	public function getDetailContent() {
		$detail_content = $this->getData("detail_content");

		Mage::getSingleton('core/session', array('name'=>'adminhtml'));
		if (! is_null(Mage::registry("_singleton/admin/session"))) {
			if(Mage::getSingleton('admin/session')->isLoggedIn()){ /*Is admin*/
			  //do stuff
				return $detail_content;
			}
		}

		$processor = Mage::helper('cms')->getPageTemplateProcessor();
		return $processor->filter($detail_content);
	}
	public function getDescription() {
		$description = $this->getData("description");

		Mage::getSingleton('core/session', array('name'=>'adminhtml'));
		if (! is_null(Mage::registry("_singleton/admin/session"))) {
			if(Mage::getSingleton('admin/session')->isLoggedIn()){ /*Is admin*/
			  //do stuff
				return $description;
			}
		}

		$processor = Mage::helper('cms')->getPageTemplateProcessor();
		return $processor->filter($description);
	}
}
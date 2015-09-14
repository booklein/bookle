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
class Ves_Blog_Helper_Data extends Mage_Core_Helper_Abstract {

	public function checkAvaiable( $controller_name = ""){
		$arr_controller = array(  "Mage_Cms",
			"Mage_Catalog",
			"Mage_Tag",
			"Mage_Checkout",
			"Mage_Customer",
			"Mage_Wishlist",
			"Mage_CatalogSearch",
			"Ves_Blog" );
		if( !empty($controller_name)){
			if( in_array( $controller_name, $arr_controller ) ){
				return true;
			}
		}
		return false;
	}

	public function getBlogPerPage(){
		return 6;

	}
	public function checkMenuItem( $menu_name = "", $menuAssignment)
	{
		if(!empty( $menu_name)  ){
			$menus = isset( $menuAssignment ) ? $menuAssignment : "all";
			$menus = explode(",", $menus);
			if( in_array("all", $menus) || in_array( $menu_name, $menus) ){
				return true;
			}
		}
		return false;
	}
	public function getListMenu(){
		$arrayParams = array(
			'all'=>Mage::helper('adminhtml')->__("All"),
			'Mage_Cms_index'=>Mage::helper('adminhtml')->__("Mage Cms Index"),
			'Mage_Cms_page'=>Mage::helper('adminhtml')->__("Mage Cms Page"),
			'Mage_Catalog_category'=>Mage::helper('adminhtml')->__("Mage Catalog Category"),
			'Mage_Catalog_product'=>Mage::helper('adminhtml')->__("Mage Catalog Product"),
			'Mage_Customer_account'=>Mage::helper('adminhtml')->__("Mage Customer Account"),
			'Mage_Wishlist_index'=>Mage::helper('adminhtml')->__("Mage Wishlist Index"),
			'Mage_Customer_address'=>Mage::helper('adminhtml')->__("Mage Customer Address"),
			'Mage_Checkout_cart'=>Mage::helper('adminhtml')->__("Mage Checkout Cart"),
			'Mage_Checkout_onepage'=>Mage::helper('adminhtml')->__("Mage Checkout"),
			'Mage_CatalogSearch_result'=>Mage::helper('adminhtml')->__("Mage Catalog Search"),
			'Mage_Tag_product'=>Mage::helper('adminhtml')->__("Mage Tag Product")
			);
		return $arrayParams;
	}
	function get( $attributes = NULL)
	{
		$data = array();
		$arrayParams = array('show',
			'enable_jquery',
			'name',
			'title',
			'mage_folder',
			'theme',
			'moduleHeight',
			'moduleWidth',
			'thumbnailMode',
			'thumbWidth',
			'thumbHeight',
			'blockPosition',
			'customBlockPosition',
			'blockDisplay',
			'menuAssignment',
			'source',
			'image_folder',
			'catsid',
			'sourceProductsMode',
			'quanlity',
			'list_show_hits',
			'imagecategory',

			'titleMaxchar',
			'descMaxchar',
			'slide_width',
			'slide_height',
			'auto_play',
			'scroll_items',
			'limit_cols',
			'show_button',
			'show_pager',
			'show_title',
			'show_desc',
			'show_price',
			'show_pricewithout',
			'show_date',
			'show_addcart',
			'duration',

			);


		foreach ($arrayParams as $var)
		{
			$tags = array('ves_blog', 'effect_setting', 'file_source_setting','image_source_setting', 'main_blog');
			foreach($tags as $tag){
				if(Mage::getStoreConfig("ves_blog/$tag/$var")!=""){
					$data[$var] =  Mage::getStoreConfig("ves_blog/$tag/$var");
				}
			}
			if(isset($attributes[$var]))
			{
				$data[$var] =  $attributes[$var];
			}
		}

		return $data;
	}
	public function getImageUrl($url = null) {
		return Mage::getSingleton('ves_blog/config')->getBaseMediaUrl() . $url;
	}
	public function resizeImage( $image, $type="l", $width , $height, $storeid= null, $quality = 100  ){

		$image= str_replace("/",DS, $image);
		$_imageUrl = Mage::getBaseDir('media').DS.$image;
		if($storeid === null) {
			$storeid = Mage::app()->getStore()->getId();
		}
		if(!$storeid){
			$imageResized = Mage::getBaseDir('media').DS."resized".DS.$type."-".$width."-".$height.DS.$image;
		}else{
			$image2 = str_replace("blog".DS,"", $image);
			$image = "blog".DS.$storeid.DS.$image2;
			$imageResized = Mage::getBaseDir('media').DS."resized".DS.$type."-".$width."-".$height.DS."blog".DS.$storeid.DS.$image2;
		}
		if (!file_exists($imageResized) && file_exists($_imageUrl)) {

			$imageObj = new Varien_Image($_imageUrl);
			$imageObj->quality($quality);
			$imageObj->constrainOnly(true);
			$imageObj->keepAspectRatio(false);
			$imageObj->keepFrame(false);
			$imageObj->keepTransparency(true);
			$imageObj->resize( $width, $height);
			$imageObj->save($imageResized);

		}

		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'resized/'."{$type}-{$width}-{$height}/".str_replace(DS,"/",$image);
	}

    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * @param mixed $valueToEncode
     * @param  boolean $cycleCheck Optional; whether or not to check for object recursion; off by default
     * @param  array $options Additional options used during encoding
     * @return string
     */
    public function jsonEncode($valueToEncode, $cycleCheck = false, $options = array())
    {
    	$json = Zend_Json::encode($valueToEncode, $cycleCheck, $options);
    	/* @var $inline Mage_Core_Model_Translate_Inline */
    	$inline = Mage::getSingleton('core/translate_inline');
    	if ($inline->isAllowed()) {
    		$inline->setIsJson(true);
    		$inline->processResponseBody($json);
    		$inline->setIsJson(false);
    	}

    	return $json;
    }

	 /*
     * Recursively searches and replaces all occurrences of search in subject values replaced with the given replace value
     * @param string $search The value being searched for
     * @param string $replace The replacement value
     * @param array $subject Subject for being searched and replaced on
     * @return array Array with processed values
     */
	 public function recursiveReplace($search, $replace, $subject)
	 {
	 	if(!is_array($subject))
	 		return $subject;

	 	foreach($subject as $key => $value)
	 		if(is_string($value))
	 			$subject[$key] = str_replace($search, $replace, $value);
	 		elseif(is_array($value))
	 			$subject[$key] = self::recursiveReplace($search, $replace, $value);

	 		return $subject;
	 	}

	 	public function getCategoriesList( $default=true ){
	 		$output = array();
	 		if( $default ){
	 			$output[0] = $this->__("Select A Category");
	 		}

	 		$collection =  Mage::getModel('ves_blog/category')->getCollection();

	 		if( $collection ){
	 			foreach( $collection as $category ){
	 				$output[$category->getId()]=$category->getTitle();
	 			}
	 		}
	 		return $output;
	 	}

	 	public function getTagUrl($tag){
	 		return Mage::getBaseUrl().Mage::getStoreConfig('ves_blog/general_setting/route')."/tag/".$tag.".html";
	 	}

	 	public function getAuthorUrl($author) {
	 		return Mage::getBaseUrl().Mage::getStoreConfig('ves_blog/general_setting/route')."/author/".$author.".html";
	 	}
	 	public function getArchiveUrl($year){
	 		return Mage::getBaseUrl().Mage::getStoreConfig('ves_blog/general_setting/route')."/archive/".$year.".html";
	 	}

	 	public function getPostURL( $id ){
	 		return Mage::getBaseUrl().Mage::getModel('core/url_rewrite')->loadByIdPath('venusblog/post/'.$id)->getRequestPath();
	 	}

	 	public function getImageSizeModes() {
	 		return array("extralarge_imagesize"=>"xl",
	 			"large_imagesize"=>"l",
	 			"medium_imagesize"=>"m",
	 			"small_imagesize"=>"s");
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

	 	public function getImportPath($theme = ""){
	 		$path = Mage::getBaseDir('var') . DS . 'cache'.DS;

	 		if (is_dir_writeable($path) != true) {
	 			mkdir ($path, '0744', $recursive  = true );
        } // end

        return $path;
    }

	/**
     * Handles CSV upload
     * @return string $filepath
     */
	public function getUploadedFile() {
		$filepath = null;
		if(isset($_FILES['importfile']['name']) and (file_exists($_FILES['importfile']['tmp_name']))) {
			try {
				$uploader = new Varien_File_Uploader('importfile');
                $uploader->setAllowedExtensions(array('csv','txt', 'json', 'xml')); // or pdf or anything
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                $path = Mage::helper('ves_blog')->getImportPath();
                $file_type = "csv";
                if(isset($_FILES['importfile']['tmp_name']['type']) && $_FILES['importfile']['tmp_name']['type'] == "application/json") {
                	$file_type = "json";
                }
                $uploader->save($path, "ves_blog_import_data.".$file_type);
                $filepath = $path . "ves_blog_import_data.".$file_type;
            } catch(Exception $e) {
            	Mage::logException($e);
            }
        }
        return $filepath;
    }
}
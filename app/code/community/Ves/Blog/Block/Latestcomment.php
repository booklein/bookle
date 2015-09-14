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
class Ves_Blog_Block_Latestcomment extends Ves_Blog_Block_List
{


	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{
		//die("AAAAAAAAA");

		parent::__construct( $attributes );

		$my_template = "";
		if(isset($attributes['template']) && $attributes['template']) {
			$my_template = $attributes['template'];
		}elseif($this->hasData("template")) {
			$my_template = $this->getData("template");
		}else {
			$my_template = "ves/blog/block/lcomment.phtml";
		}

		$this->setTemplate($my_template);
	}

	public function _toHtml(){
		if(!$this->getConfig("enable_latest_comment")) {
			return ;
		}

		$collection = Mage::getModel( 'ves_blog/comment' )
		->getCollection();

		$store_id = Mage::app()->getStore()->getId();
		if($store_id){
			$collection->addStoreFilter($store_id);
		}

	    //$collection->addCategoriesFilter(0);

		$collection ->setOrder( 'created', 'DESC' );

		$collection->setPageSize( $this->getConfig("latest_comment_limit") )->setCurPage( 1 );

		$this->assign( 'comments', $collection );


		return parent::_toHtml();

	}
	public function getCountingComment( $post_id = 0){

		$comment = Mage::getModel('ves_blog/comment')->getCollection()
		->addEnableFilter( 1  )
		->addPostFilter( $post_id  );
		return count($comment);
	}

	public function subString( $text, $length = 100, $replacer ='...', $is_striped=true ){
		$text = trim($text);
		$text = ($is_striped==true)?strip_tags($text):$text;
		if(strlen($text) <= $length){
			return $text;
		}
		$text = substr($text,0,$length);
		$pos_space = strrpos($text,' ');
		return substr($text,0,$pos_space).$replacer;
	}
}
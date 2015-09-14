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
class Ves_Blog_Block_Latest extends Ves_Blog_Block_List
{

	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{

		parent::__construct( $attributes );

		$my_template = "";
		if(isset($attributes['template']) && $attributes['template']) {
			$my_template = $attributes['template'];
		}elseif($this->hasData("template")) {
			$my_template = $this->getData("template");
		}else {
			$my_template = "ves/blog/block/latest.phtml";
		}

		$this->setTemplate($my_template);
	}

	public function _toHtml(){
		if(!$this->getConfig("enable_latestmodule")) {
			return ;
		}
		$collection = Mage::getModel( 'ves_blog/post' )
		->getCollection();
		$catsid = $this->getConfig('catsid');
		if($catsid){
			$catsid = explode(',', $catsid);
			$collection->addCategoriesFilter($catsid);
		}

		if( $this->getConfig("latest_typesource") == "hit" ){
			$collection ->setOrder( 'hits', 'DESC' );
		}else {
			$collection ->setOrder( 'created', 'DESC' );
		}

		$collection->setPageSize( $this->getConfig("limit_items") )->setCurPage( 1 );
		$this->assign( 'posts', $collection );

		return parent::_toHtml();

	}
	public function getCountingComment( $post_id = 0){

		$comment = Mage::getModel('ves_blog/comment')->getCollection()
		->addEnableFilter( 1  )
		->addPostFilter( $post_id  );
		return count($comment);
	}
}
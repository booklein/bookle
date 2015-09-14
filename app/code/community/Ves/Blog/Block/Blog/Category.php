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
class Ves_Blog_Block_Blog_Category extends Ves_Blog_Block_Blog_Template
{
	private $category;
	var $_layout_mode = "";
	public function __construct($attributes = array())
	{
		parent::__construct( $attributes );

		$allow_custom_layout = false;

		$mode = $this->getRequest()->getParam( "mode" );
		if($mode) {
			Mage::getModel('core/cookie')->set("ves_blog_layout_mode", $mode, time()+86400,'/');
		} else {
			$mode = Mage::getModel('core/cookie')->get("ves_blog_layout_mode");
		}

		if(!$mode) {
			$mode = $this->getListConfig("list_layout_mode");
			$allow_custom_layout = true;
		}

		$this->_layout_mode = $mode;

		if($allow_custom_layout && $this->hasData("template") && $this->getData("template")) {
			$this->setTemplate($this->getData("template"));
		} else {
			switch ($mode) {
				case 'list':
				case 'grid':
				$this->setTemplate("ves/blog/layout/default.phtml");
				break;
				case 'second':
				$this->setTemplate("ves/blog/layout/second.phtml");
				break;
				case 'custom':
				$this->setTemplate("ves/blog/layout/custom.phtml");
				break;
				case 'thumb_view':
				$this->setTemplate("ves/blog/layout/thumb_view.phtml");
				break;
				case 'masonry':
				$this->setTemplate("ves/blog/layout/masonry.phtml");
				break;
				default:
				$this->setTemplate("ves/blog/list.phtml");
				break;
			}
		}
	}
	public function getLayoutMode() {
		return $this->_layout_mode;
	}
	public function _toHtml(){
		$grid_col_ls = $this->getListConfig("grid_col_ls");
		$grid_col_ls = $grid_col_ls?(int)$grid_col_ls:3;
		$grid_col_ms = $this->getListConfig("grid_col_ms");
		$grid_col_ms = $grid_col_ms?(int)$grid_col_ms:3;
		$grid_col_ss = $this->getListConfig("grid_col_ss");
		$grid_col_ss = $grid_col_ss?(int)$grid_col_ss:2;
		$grid_col_mss = $this->getListConfig("grid_col_mss");
		$grid_col_mss = $grid_col_mss?(int)$grid_col_mss:1;

		$second_image_col = $this->getListConfig("second_image_col");
		$second_image_col = $second_image_col?(int)$second_image_col:6;
		$second_content_col = $this->getListConfig("second_content_col");
		$second_content_col = $second_content_col?(int)$second_content_col:6;

		$this->assign("grid_col_ls", $grid_col_ls);
		$this->assign("grid_col_ms", $grid_col_ms);
		$this->assign("grid_col_ss", $grid_col_ss);
		$this->assign("grid_col_mss", $grid_col_mss);
		$this->assign("second_image_col", $second_image_col);
		$this->assign("second_content_col", $second_content_col);

		return parent::_toHtml();
	}

	protected function _prepareLayout() {
		$id = $this->getRequest()->getParam('id');
		$this->category = Mage::getModel('ves_blog/category')->load( $id );
		$this->getCountingPost();
		$this->setType( "category" )
		->setPageTitle( $this->category->getTitle() )
		->setHeadInfo( $this->category->getMetaKeyword(), $this->category->getMetaDescription() );


		$breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
		$breadcrumbs->addCrumb( 'home', array( 'label'=>Mage::helper('ves_blog')->__('Home'),
			'title'=>Mage::helper('ves_blog')->__('Go to Home Page'),
			'link' => Mage::getBaseUrl()) );

		$extension = "";
		$breadcrumbs->addCrumb( 'venus_blog', array( 'label' => $this->getGeneralConfig("title"),
			'title' => $this->getGeneralConfig("title"),
			'link'  =>  Mage::getBaseUrl().$this->getGeneralConfig("route").$extension ) );

		$breadcrumbs->addCrumb( 'blogcategory_title', array( 'label'=> $this->category->getTitle(),
			'title'=>$this->category->getTitle(),
			'link' => $this->category->getCategoryLink()) );
	}

	public function getCategory(){


		return $this->category ;
	}
	public function getChildrent(){
		$id = $this->getRequest()->getParam('id');
		$collection = Mage::getModel('ves_blog/category')
		->getCollection()
		->addEnableFilter()
		->addChildrentFilter( $id )
		->setOrder("position","DESC");

		return $collection;
	}

	public function countPosts( $categoryId ){
		$collection = Mage::getModel( 'ves_blog/post' )
							->getCollection()
							->addCategoryFilter( $categoryId );
		return $collection->count();
	}
	public function getCountingPost(){
		$id = $this->getRequest()->getParam('id');

		$collection = Mage::getModel( 'ves_blog/post' )
						->getCollection()
						->addCategoryFilter( $id );

		if($this->_layout_mode) {
			$limit = (int)$this->getListConfig("list_limit");
		} else {
			$limit = (int)$this->getListConfig("list_leadinglimit") + (int)$this->getListConfig("list_secondlimit");
		}

		Mage::register( 'paginateTotal', count($collection) );
		Mage::register( "paginateLimitPerPage", $limit );
	}

	public function getPosts(){

		$id = $this->getRequest()->getParam('id');
		$page = $this->getRequest()->getParam('page') ? $this->getRequest()->getParam('page') : 1;

		if($this->_layout_mode) {
			$limit = (int)$this->getListConfig("list_limit");
		} else {
			$limit = (int)$this->getListConfig("list_leadinglimit") + (int)$this->getListConfig("list_secondlimit");
		}

		$orderby = $this->getRequest()->getParam( "orderby" );
		$orderway = $this->getRequest()->getParam( "orderway" );
		$default_orderby = $this->getListConfig("sort_orderby");
		$default_orderway = $this->getListConfig("sort_orderway");

		if(!$default_orderby) {
			$default_orderby = "created";
		}

		if(!$default_orderway) {
			$default_orderway = "DESC";
		}

		if(!$orderby && !$orderway) {
			$orderby = $default_orderby;
			$orderway = $default_orderway;
		} elseif($orderby && !$orderway) {
			$orderway = $default_orderway;
		} elseif(!$orderby && $orderway) {
			$orderby = $default_orderby;
		}

		$collection = Mage::getModel( 'ves_blog/post' )
						->getCollection()
						->addCategoryFilter( $id )
						->setOrder( $orderby, $orderway )
						->setPageSize( $limit )
						->setCurPage( $page );

		return $collection;
	}
	public function getCountingComment( $post_id = 0){

		$comment = Mage::getModel('ves_blog/comment')->getCollection()
		->addEnableFilter( 1  )
		->addPostFilter( $post_id  );
		return count($comment);
	}
}
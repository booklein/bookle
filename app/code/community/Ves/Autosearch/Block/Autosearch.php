<?php
/*------------------------------------------------------------------------
 # VenusTheme Pagebuilder Module 
 # ------------------------------------------------------------------------
 # author:    VenusTheme.Com
 # copyright: Copyright (C) 2012 http://www.venustheme.com. All Rights Reserved.
 # @license: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Websites: http://www.venustheme.com
 # Technical Support:  http://www.venustheme.com/
-------------------------------------------------------------------------*/
//require_once(Mage::getBaseDir('code').'/community/Ves/Pagebuider/Helper/widgetbase.php');
class Ves_Autosearch_Block_Autosearch extends Mage_Core_Block_Template 
{	
	var $_config = array();
	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{	
		/*End init meida files*/

		$this->_controller = 'autosearch';
		$this->convertAttributesToConfig($attributes);
		if( !$this->getConfig('show') ) return;

		parent::__construct();

		if($this->hasData("template")) {
			$this->setTemplate( $this->getData("template") );
		} else {
			$template = 'ves/autosearch/default.phtml';
			if( $this->getConfig( "template" ) ){
				$template = $this->getConfig( "template" );
			}
			$this->setTemplate( $template );
		}

		/*Cache Block*/
        $enable_cache = $this->getConfig("enable_cache", "ves_autosearch", 1 );
        if(!$enable_cache) {
          $cache_lifetime = null;
        } else {
          $cache_lifetime = $this->getConfig("cache_lifetime", 86400 );
          $cache_lifetime = (int)$cache_lifetime>0?$cache_lifetime: 86400;
        }
        
        $this->addData(array('cache_lifetime' => $cache_lifetime));
        $this->addCacheTag(array(
          Mage_Core_Model_Store::CACHE_TAG,
          Mage_Cms_Model_Block::CACHE_TAG,
          Ves_Autosearch_Model_Search::CACHE_TAG
        ));

        /*End Cache Block*/
	}

	/**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
           'VES_AUTOSEARCH_BLOCK_AUTOSEARCH',
           $this->getNameInLayout(),
           Mage::app()->getStore()->getId(),
           Mage::getDesign()->getPackageName(),
           Mage::getDesign()->getTheme('template'),
           Mage::getSingleton('customer/session')->getCustomerGroupId(),
           'template' => $this->getTemplate(),
        );
    }

	public function convertAttributesToConfig($attributes = array()) {
      if($attributes) {
        foreach($attributes as $key=>$val) {
            $this->setConfig($key, $val);
        }
      }
    }

	public function listProductLink(){
    	return $this->getUrl('vesautosearch/index/ajaxgetproduct');
  	}
	/**
     * Rendering block content
     *
     * @return string
     */
	function _toHtml() 
	{

		if( !$this->getConfig('show') ) return;

		$categories = "";
		$searchCollection = "";

		if($this->getConfig('show_filter_category')) {

			$rootCatId = Mage::app()->getStore()->getRootCategoryId();
			$categories = $this->getTreeCategories($rootCatId, 0);
		}

		if($this->getConfig('enable_search_term','enable_search_terms')) {
			$limit = $this->hasData('limit_term')?$this->getData('limit_term'):$this->getConfig('limit','enable_search_terms');
			$searchCollection = $this->getListSearchTerms((int)$limit);
		}

		$this->assign( "categories", $categories );
		$this->assign( "limit", $this->getConfig("limit"));
		$this->assign( "thumb_width", $this->getConfig("thumbWidth"));
		$this->assign( "thumb_height", $this->getConfig("thumbHeight"));
		$this->assign( "searchCollection", $searchCollection );
		$this->assign( "listProductLink", $this->listProductLink() );
		$this->assign( "prefix", $this->getConfig('prefix') );
		$this->assign( "show_filter_category", $this->getConfig('show_filter_category') );
		$this->assign( "show_image", $this->getConfig('show_image') );
		$this->assign( "show_price", $this->getConfig('show_price') );

        return parent::_toHtml();
	}

	/**
	 * get value of the extension's configuration
	 *
	 * @return string 
	 */
	function getConfig( $key, $panel='ves_autosearch', $default ="" ){

		$return = "";
	    $value = $this->getData($key);
	    //Check if has widget config data
	    if($this->hasData($key) && $value !== null) {

	      if($value == "true") {
	        return 1;
	      } elseif($value == "false") {
	        return 0;
	      }
	      
	      return $value;
	      
	    } else {

	      if(isset($this->_config[$key])){
	        $return = $this->_config[$key];
	        if($return == "true") {
	            $return = 1;
	        } elseif($return == "false") {
	            $return = 0;
	        }
	      }else{
	        $return = Mage::getStoreConfig("ves_autosearch/$panel/$key");
	      }
	      if(($return == "" || $return == null) && $default) {
	        $return = $default;
	      }

	    }

	    return $return;
	}


	/**
     * overrde the value of the extension's configuration
     *
     * @return string
     */
    function setConfig($key, $value) {
    if($value == "true") {
          $value =  1;
      } elseif($value == "false") {
          $value = 0;
      }
      if($value != "") {
          $this->_config[$key] = $value;
      }
      return $this;
    }

	public function getCatalogSearchLink() {
		return $this->getUrl('catalogsearch/result/');
	}


	public function getListSearchTerms($limit = 0) {
		$terms = Mage::getResourceModel('catalogsearch/query_collection')
                ->setPopularQueryFilter( Mage::app()->getStore()->getId() )
                /*->setOrder('num_results', 'DESC')*/
                ->setPageSize($limit)
                ->load()
                ->getItems();
		return $terms;
	}

	public function getTreeCategories($parentId, $level = 0, $caret = '&nbsp;&nbsp;'){
		$category_id = $this->getRequest()->getParam("cat");

	    $allCats = Mage::getModel('catalog/category')->getCollection()
	                ->addAttributeToSelect('*')
	                ->addAttributeToFilter('is_active','1')
	                ->addAttributeToFilter('include_in_menu','1')
	                ->addAttributeToFilter('parent_id',array('eq' => $parentId));
	    $html = '';
	    $prefix = '';
	    //$children = Mage::getModel('catalog/category')->getCategories(7);
	    if($level) {
	    	for($i=0;$i < $level; $i++) {
	    		$prefix .= $caret;
	    	}
	    }
	    foreach ($allCats as $category) 
	    {
	    	$html .= '<option value="'.$category->getId().'" '.($category_id == $category->getId() ? 'selected="selected"':'') .'>'.$prefix.$category->getName().'</option>';
	        $subcats = $category->getChildren();
	        if($subcats != ''){
	            $html .= $this->getTreeCategories($category->getId(), (int)$level + 1, $caret.'&nbsp;');
	        }

	    }
	    return $html;
	}
}
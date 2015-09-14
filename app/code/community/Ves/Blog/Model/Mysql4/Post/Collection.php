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
class Ves_Blog_Model_Mysql4_Post_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    /**
     * Constructor method
     */
    protected function _construct() {
        parent::_construct();
        $this->_init('ves_blog/post');
    }

    /**
     * Add Filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @return Ves_Slider_Model_Mysql4_Post_Collection
     */
    public function addStoreFilter($store) {

        if (!Mage::app()->isSingleStoreMode()) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = $store->getId();
            }

            $this->getSelect()->join(
                array('store_table' => $this->getTable('ves_blog/post_store')),
                'main_table.post_id = store_table.post_id',
                array()
                )
            ->where('store_table.store_id in (?)', array(0, $store));
            return $this;
        }
        return $this;
    }

    /**
     * Add Filter by status
     *
     * @param int $status
     * @return Ves_Slider_Model_Mysql4_Post_Collection
     */
    public function addEnableFilter($status = 1) {
        $this->getSelect()->where('main_table.is_active = ?', $status);
        return $this;
    }
    public function addAuthorFilter( $userId ) {
        $this->getSelect()
        ->where('main_table.user_id = ?', $userId);

        return $this;
    }
    public function  addCategoriesFilter( $categoryId ){
        if($categoryId) {
            $this->getSelect()->join(
                array('cate' => $this->getTable('ves_blog/category')),
                'main_table.category_id = cate.category_id',
                array("cate.title as cat_title")
                )
            ->where('main_table.category_id in (?)', $categoryId);
        }

        return $this;
    }
    public function addCategoryFilter( $categoryId ) {
        $this->getSelect()
        ->where( 'main_table.category_id = ?', $categoryId );

        return $this;
    }

    public function addPostTagFilter( ) {
        $this->getSelect()
        ->where( "TRIM(IFNULL(main_table.tags,'')) <> ''" );

        return $this;
    }

    public function addTagsFilter( $tags ){
        $condition = array();
        $collection = 	$this->getSelect();
        $where = array();
        foreach( $tags as $tag ) {
            $where[] = ' main_table.tags like "%'.trim($tag).'%" ';
        }
        $where = implode(" OR ", $where);
        $collection->where( $where );
        return $this;
    }

    public function addKeywordFilter( $key_word ){
        $collection =   $this->getSelect();
        $resHelper = Mage::getResourceHelper('core');
        $likeOptions = array('position' => 'any');
        $where = array();

        $where[] = $resHelper->getCILike("main_table.tags", $key_word, $likeOptions);
        $where[] = $resHelper->getCILike("main_table.title", $key_word, $likeOptions);
        $where[] = $resHelper->getCILike("main_table.description", $key_word, $likeOptions);
        $where[] = $resHelper->getCILike("main_table.detail_content", $key_word, $likeOptions);
        $where = implode(" OR ", $where);
        $collection->where( $where );
        return $this;
    }

    public function addArchivesFilter( $archive = "") {
        $archive = trim($archive);
        if($archive) {
            $collection =   $this->getSelect();
            $tmp = explode("_", $archive);
            if(count($tmp) > 1) {
                $year = $tmp[0];
                $month = $tmp[1];

                $collection->where( " YEAR(main_table.created) = '".$year."'");
                $collection->where( " MONTH(main_table.created) = '".$month."'");
            } else {
                $year = $tmp[0];
                $collection->where( " YEAR(main_table.created) = '".$year."'");
            }
        }
        return $this;
    }

    public function addIdFilter( $post_id, $where_code = "!="){
        $condition = array();
        $collection =   $this->getSelect();
        $collection->where( ' main_table.post_id '.$where_code.(int)$post_id);

        return $this;
    }

    /**
     * After load processing - adds store information to the datasets
     *
     */
    protected function _beforeLoad()
    {
        $store_id = Mage::app()->getStore()->getId();
        if($store_id){
            $this->addStoreFilter($store_id);
        }
        parent::_beforeLoad();
    }

    protected function _afterLoad(){
        
        parent::_afterLoad();

        return $this;
    }

}
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
class Ves_Blog_Model_Mysql4_Category_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    /**
     * Constructor method
     */
    protected function _construct() {
      parent::_construct();
      $this->_init('ves_blog/category');
  }


    /**
     * Add Filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @return Ves_Slider_Model_Mysql4_Category_Collection
     */
    public function addStoreFilter($store) {
        if (!Mage::app()->isSingleStoreMode()) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }

            $this->getSelect()->join(
                array('store_table' => $this->getTable('ves_blog/category_store')),
                'main_table.category_id = store_table.category_id',
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
     * @return Ves_Slider_Model_Mysql4_Category_Collection
     */
    public function addEnableFilter($status = 1) {
        $this->getSelect()->where('main_table.is_active = ?', $status);
        return $this;
    }

    public function addChildrentFilter( $parentId = 1) {
        $this->getSelect()->where( 'main_table.parent_id = ?', $parentId );
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
}
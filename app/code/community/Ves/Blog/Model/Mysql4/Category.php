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
class Ves_Blog_Model_Mysql4_Category extends Mage_Core_Model_Mysql4_Abstract {

    /**
     * Initialize resource model
     */
    protected function _construct() {

      $this->_init('ves_blog/category', 'category_id');
    }


    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object) {
      $select = parent::_getLoadSelect($field, $value, $object);

      return $select;
    }

    /**
     * Call-back function
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object) {
        // Cleanup stats on blog delete
      $adapter = $this->_getReadAdapter();
      $identifier = $object->getData('identifier');
      $identifier = trim($identifier);
      $identifier = strtolower($identifier);
      $identifier = str_replace(' ','-', $identifier);
      $object->setData('identifier', $identifier);

      return parent::_beforeSave($object);
    }

     /**
     * Assign page to store views
     *
     * @param Mage_Core_Model_Abstract $object
     */
     protected function _afterSave(Mage_Core_Model_Abstract $object)
     {
      $condition = $this->_getWriteAdapter()->quoteInto('category_id = ?', $object->getId());
      // process faq item to store relation
      $this->_getWriteAdapter()->delete($this->getTable('ves_blog/category_store'), $condition);
      $stores = (array) $object->getData('stores');

      if($stores){
        foreach ((array) $object->getData('stores') as $store) {
          $storeArray = array ();
          $storeArray['category_id'] = $object->getId();
          $storeArray['store_id'] = $store;
          $this->_getWriteAdapter()->insert(
            $this->getTable('ves_blog/category_store'), $storeArray
            );
        }
      }

      return parent::_afterSave($object);
    }

  /**
   * Do store and category processing after loading
   *
   * @param Mage_Core_Model_Abstract $object Current faq item
   */
  protected function _afterLoad(Mage_Core_Model_Abstract $object)
  {
      // process faq item to store relation
    $select = $this->_getReadAdapter()->select()->from(
      $this->getTable('ves_blog/category_store')
      )->where('category_id = ?', $object->getId());

    if ($data = $this->_getReadAdapter()->fetchAll($select)) {
      $storesArray = array ();
      foreach ($data as $row) {
        $storesArray[] = $row['store_id'];
      }
      $object->setData('store_id', $storesArray);
    }

    return parent::_afterLoad($object);
  }

  public function lookupStoreIds($category_id = 0){
    $select = $this->_getReadAdapter()->select()->from(
      $this->getTable('ves_blog/category_store')
      )->where('category_id = ?', (int)$category_id);

    $storesArray = array ();

    if ($data = $this->_getReadAdapter()->fetchAll($select)) {

      foreach ($data as $row) {
        $storesArray[] = $row['store_id'];
      }
    }
    return $storesArray;
  }

}

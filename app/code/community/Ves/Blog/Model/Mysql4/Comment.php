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
class Ves_Blog_Model_Mysql4_Comment extends Mage_Core_Model_Mysql4_Abstract {

    /**
     * Initialize resource model
     */
    protected function _construct() {

      $this->_init('ves_blog/comment', 'comment_id');
    }

    /**
     * Load images
     */
   // public function loadImage(Mage_Core_Model_Abstract $object) {
   //     return $this->__loadImage($object);
   // }

        /**
     * Assign page to store views
     *
     * @param Mage_Core_Model_Abstract $object
     */
        protected function _afterSave(Mage_Core_Model_Abstract $object)
        {
          $condition = $this->_getWriteAdapter()->quoteInto('comment_id = ?', $object->getId());
      // process faq item to store relation
          $this->_getWriteAdapter()->delete($this->getTable('ves_blog/comment_store'), $condition);
          $stores = (array) $object->getData('stores');

          if($stores){
            foreach ((array) $object->getData('stores') as $store) {
              $storeArray = array ();
              $storeArray['comment_id'] = $object->getId();
              $storeArray['store_id'] = $store;
              $this->_getWriteAdapter()->insert(
                $this->getTable('ves_blog/comment_store'), $storeArray
                );
            }
          }else{
            $storeArray = array ();
            $storeArray['comment_id'] = $object->getId();
            $storeArray['store_id'] = $object->getStoreId();
            $this->_getWriteAdapter()->insert(
              $this->getTable('ves_blog/comment_store'), $storeArray
              );
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
      $this->getTable('ves_blog/comment_store')
      )->where('comment_id = ?', $object->getId());

    if ($data = $this->_getReadAdapter()->fetchAll($select)) {
      $storesArray = array ();
      foreach ($data as $row) {
        $storesArray[] = $row['store_id'];
      }
      $object->setData('store_id', $storesArray);
    }

    return parent::_afterLoad($object);
  }

  public function lookupStoreIds($comment_id = 0){
    $select = $this->_getReadAdapter()->select()->from(
      $this->getTable('ves_blog/comment_store')
      )->where('comment_id = ?', (int)$post_id);

    $storesArray = array ();

    if ($data = $this->_getReadAdapter()->fetchAll($select)) {

      foreach ($data as $row) {
        $storesArray[] = $row['store_id'];
      }
    }
    return $storesArray;
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
    protected function _beforeDelete(Mage_Core_Model_Abstract $object) {
        // Cleanup stats on blog delete
      $adapter = $this->_getReadAdapter();
        // 1. Delete blog/store
        //$adapter->delete($this->getTable('ves_blog/comment_store'), 'comment_id='.$object->getId());
        // 2. Delete blog/post_cat

      return parent::_beforeDelete($object);
    }

  }

<?php
 /*------------------------------------------------------------------------
  # VenusTheme Brand Module 
  # ------------------------------------------------------------------------
  # author:    VenusTheme.Com
  # copyright: Copyright (C) 2012 http://www.venustheme.com. All Rights Reserved.
  # @license: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.venustheme.com
  # Technical Support:  http://www.venustheme.com/
-------------------------------------------------------------------------*/
class Ves_BlockBuilder_Model_Mysql4_Block extends Mage_Core_Model_Mysql4_Abstract {

    /**
     * Initialize resource model
     */
    protected function _construct() {
	
        $this->_init('ves_blockbuilder/block', 'block_id');
    }

    /**
     * Load images
     */
   // public function loadImage(Mage_Core_Model_Abstract $object) {
   //     return $this->__loadImage($object);
   // }

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
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Page
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object) {
        /*
         * For two attributes which represent timestamp data in DB
         * we should make converting such as:
         * If they are empty we need to convert them into DB
         * type NULL so in DB they will be empty and not some default value
         */
        if($object->getData("block_type") == "page") {
          if (!$this->getIsUniquePageToStores($object)) {
              Mage::throwException(Mage::helper('cms')->__('A page URL key for specified store already exists.'));
          }

          if (!$this->isValidPageIdentifier($object)) {
              Mage::throwException(Mage::helper('cms')->__('The page URL key contains capital letters or disallowed symbols.'));
          }

          if ($this->isNumericPageIdentifier($object)) {
              Mage::throwException(Mage::helper('cms')->__('The page URL key cannot consist only of numbers.'));
          }
        }
        return parent::_beforeSave($object);
    }
    /**
     * Call-back function
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object) {
        // Cleanup stats on brand delete
        $adapter = $this->_getReadAdapter();
        // 1. Delete brand/store
        //$adapter->delete($this->getTable('venustheme_brand/brand_store'), 'brand_id='.$object->getId());
        // 2. Delete brand/post_cat

        return parent::_beforeDelete($object);
    }
    /**
   * Assign page to store views
   *
   * @param Mage_Core_Model_Abstract $object
   */
  protected function _afterSave(Mage_Core_Model_Abstract $object)
  {
    // Code that flushes cache goes here
    Mage::app()->cleanCache( array(
        Mage_Core_Model_Store::CACHE_TAG,
        Mage_Cms_Model_Block::CACHE_TAG,
        Ves_BlockBuilder_Model_Block::CACHE_BLOCK_TAG
    ) );
    Mage::app()->cleanCache( array(
        Mage_Core_Model_Store::CACHE_TAG,
        Mage_Cms_Model_Block::CACHE_TAG,
        Ves_BlockBuilder_Model_Block::CACHE_PAGE_TAG
    ) );
    return parent::_afterSave($object);
  }

  /**
   * Do store and category processing after loading
   * 
   * @param Mage_Core_Model_Abstract $object Current faq item
   */
  protected function _afterLoad(Mage_Core_Model_Abstract $object)
  {
    $stores = array();
    // get cms page data
    if($alias = $object->getData("alias")) {
      $cms_page = Mage::getModel("cms/page")->load($alias, "identifier");
      if($cms_page->getPageId()) {
        $stores = $cms_page->getStoreId();
        
        $object->setData("cmspage_id", $cms_page->getPageId());
        $object->setData("root_template", $cms_page->getRootTemplate());
        $object->setData("root_template", $cms_page->getRootTemplate());
        $object->setData("layout_update_xml", $cms_page->getLayoutUpdateXml());
        $object->setData("custom_theme_from", $cms_page->getCustomThemeFrom());
        $object->setData("custom_theme_to", $cms_page->getCustomThemeTo());
        $object->setData("custom_theme", $cms_page->getCustomTheme());
        $object->setData("custom_root_template", $cms_page->getCustomRootTemplate());
        $object->setData("custom_layout_update_xml", $cms_page->getCustomLayoutUpdateXml());
        $object->setData("meta_keywords", $cms_page->getMetaKeywords());
        $object->setData("meta_description", $cms_page->getMetaDescription());
      }
    }
    if($settings = $object->getData("settings")) {
      $settings = unserialize($settings);
      if($settings) {
        foreach($settings as $key => $val) {
          $object->setData($key, $val);
        }
      }
    }
    $stores = $stores?$stores:array(0);
    $object->setData("store_id", $stores);
    
    return parent::_afterLoad($object);
  }

  /**
     * Retrieve load select with filter by alias, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return Varien_Db_Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('cp' => $this->getMainTable()))
            ->where('cp.alias = ?', $identifier)
            ->where('cp.block_type = ?', "page");

        if (!is_null($isActive)) {
            $select->where('cp.status = ?', $isActive);
        }
        return $select;
    }

   /**
     * Check for unique of alias of page to selected store(s).
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function getIsUniquePageToStores(Mage_Core_Model_Abstract $object)
    {
        if (Mage::app()->isSingleStoreMode() || !$object->hasStores()) {
            $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
        } else {
            $stores = (array)$object->getData('stores');
        }

        $select = $this->_getLoadByIdentifierSelect($object->getData('alias'), $stores);

        if ($object->getId()) {
            $select->where('cp.block_id <> ?', $object->getId());
        }

        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     *  Check whether page alias is numeric
     *
     * @date Wed Mar 26 18:12:28 EET 2008
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    protected function isNumericPageIdentifier(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('alias'));
    }

    /**
     *  Check whether page alias is valid
     *
     *  @param    Mage_Core_Model_Abstract $object
     *  @return   bool
     */
    protected function isValidPageIdentifier(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('alias'));
    }

}

<?php
class Ves_TabsHome_Model_Product_Changes{

    protected function _clean_block_cache() {
        // Code that flushes cache goes here
        Mage::app()->cleanCache( array(
            Mage_Core_Model_Store::CACHE_TAG,
            Mage_Cms_Model_Block::CACHE_TAG,
            Ves_TabsHome_Model_Product::CACHE_BLOCK_TAG
        ) );
        Mage::app()->cleanCache( array(
            Mage_Core_Model_Store::CACHE_TAG,
            Mage_Cms_Model_Block::CACHE_TAG,
            Ves_TabsHome_Model_Product::CACHE_WIDGET_TAG
        ) );
    }
    public function detectProductAttributeChanges($observer)
    {
        $this->_clean_block_cache();
        return $this;
    }

    public function detectProductChanges($observer)
    {
        /**
         * @var $product Mage_Catalog_Model_Product
         * @var $user    Mage_Admin_Model_User
         */
        $product = $observer->getEvent()->getProduct();
        if ($product->hasDataChanges()) {
            $this->_clean_block_cache();
        }
        return $this;
    }

}
<?php
class Ves_Deals_Model_Config extends Mage_Catalog_Model_Product_Media_Config {

    const CACHE_BLOCK_TAG = 'ves_deals_block_carousel';
    const CACHE_WIDGET_TAG = 'ves_deals_widget_carousel';
    
    public function getBaseMediaPath() {
        return Mage::getBaseDir('media') .DS. 'productcarousel';
    }

    public function getBaseMediaUrl() {
        return Mage::getBaseUrl('media') . 'productcarousel';
    }

    public function getBaseTmpMediaPath() {
        return Mage::getBaseDir('media') .DS. 'tmp' .DS. 'productcarousel';
    }

    public function getBaseTmpMediaUrl() {
        return Mage::getBaseUrl('media') . 'tmp/productcarousel';
    }

}
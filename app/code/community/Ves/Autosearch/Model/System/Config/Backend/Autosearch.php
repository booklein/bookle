<?php
class Ves_Autosearch_Model_System_Config_Backend_Autosearch extends Mage_Core_Model_Config_Data {
    protected function _afterSave() {
	    // Code that flushes cache goes here
        Mage::app()->cleanCache( array(
	        Mage_Core_Model_Store::CACHE_TAG,
	        Mage_Cms_Model_Block::CACHE_TAG,
	        Ves_Autosearch_Model_Search::CACHE_TAG
	    ) );
	    Mage::app()->cleanCache( array(
	        Mage_Core_Model_Store::CACHE_TAG,
	        Mage_Cms_Model_Block::CACHE_TAG,
	        Ves_Autosearch_Model_Search::CACHE_WIDGET_TAG
	    ) );
	}
}
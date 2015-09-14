<?php
/*------------------------------------------------------------------------
 # VenusTheme Block Builder Module 
 # ------------------------------------------------------------------------
 # author:    VenusTheme.Com
 # copyright: Copyright (C) 2012 http://www.venustheme.com. All Rights Reserved.
 # @license: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Websites: http://www.venustheme.com
 # Technical Support:  http://www.venustheme.com/
-------------------------------------------------------------------------*/

class Ves_BlockBuilder_Model_Block extends Mage_Core_Model_Abstract
{
	const CACHE_BLOCK_TAG              = 'ves_blockbuilder_block';
	const CACHE_PAGE_TAG              = 'ves_blockbuilder_page';
	const CACHE_MEDIA_TAG 			  = 'ves_blockbuilder_media';
	const CACHE_LIVECSS_TAG 		 	= 'ves_blockbuilder_livecss';

    protected function _construct() {	
        $this->_init('ves_blockbuilder/block');
    }

    public function getBlockByAlias($alias = "", $is_page = false) {
    	$customer_group_id = (int)Mage::getSingleton('customer/session')->getCustomerGroupId();
		if($alias) {
			$todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
			$todayDateTime = strtotime($todayDate);
			$todayDate = date("Y-m-d", $todayDateTime);

			$collection = $this->getCollection()
						->addFieldToFilter('alias', $alias)
						->addFieldToFilter('status', 1)
						->addFieldToFilter('show_from', array('or'=> array(
			                0 => array('date' => true, 'lt' => $todayDate),
			                1 => array('is' => new Zend_Db_Expr('null')))
			            ), 'left')
			            ->addFieldToFilter('show_to', array('or'=> array(
			                0 => array('date' => true, 'gteq' => $todayDate),
			                1 => array('is' => new Zend_Db_Expr('null')))
			            ), 'left');

			if($is_page) {
				$collection->addFieldToFilter('block_type', "page");
			}
			$block_entity =	$collection->getFirstItem();
					
			if($block_entity) {
				$customer_group = $block_entity->getCustomerGroup();
				$array_groups = explode(",",$customer_group);
				if($array_groups && in_array(0, $array_groups)){
					return $block_entity;
				} elseif( $array_groups && in_array($customer_group_id, $array_groups)) {
					return $block_entity;
				}
			}
			
		}
		return null;
    }

    public function checkBlockProfileAvailable( $block_profile = null ){
    	$checked = true;
    	if($block_profile) {
    		if($block_profile->getStatus() != "1") {
				$checked = false;	
    		} else {
    			$customer_group_id = (int)Mage::getSingleton('customer/session')->getCustomerGroupId();
    			$customer_group =  $block_profile->getCustomerGroup();
				$array_groups = explode(",",$customer_group);
				if($array_groups && !in_array(0, $array_groups) && !in_array($customer_group_id, $array_groups)){
					$checked = false;
				} else {
					$todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
					$todayDateTime = strtotime($todayDate);
					$date_from = $block_profile->getShowFrom();
					if($date_from) {
						$date_from = strtotime($date_from);
					} else {
						$date_from = 0;
					}
					
					$date_to = $block_profile->getShowTo();
					if($date_to) {
						$date_to = strtotime($date_to);
					} else {
						$date_to = 0;
					}

					if($date_from > $todayDateTime || ($date_to > 0 && $date_to < $todayDateTime)) {
						$checked = false;
					}
				}
				
    		}
    	}
    	return $checked;
    }
    public function getBlocksByPosition( $position = "") {

    }

    public function getCMSPageUrl() {
    	$cmspage_id = $this->getCmspageId();
    	$href = "";
    	if($cmspage_id) {
    		$href = Mage::helper('cms/page')->getPageUrl( $cmspage_id );
    	}
    	
    	return $href;
    }
}
<?php

class Ves_ProductCarousel2_Model_System_Config_Source_ListMenu
{
 public function toOptionArray()
    {
    	$helper =  Mage::helper('ves_productcarousel2/data');
    	$params = $helper->getListMenu();
    	$option = array();
    	if(!empty($params)){
    		foreach($params as $key=>$label){
    			$tmp = array();
    			$tmp["value"] = $key;
    			$tmp["label"] = $label;
    			$option[] = $tmp;
    		}
    	}
        return $option;
    }
}

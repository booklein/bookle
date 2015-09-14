<?php
class Ves_ProductCarousel2_Model_System_Config_Backend_ProductCarousel2_checkQty extends Mage_Core_Model_Config_Data
{

    protected function _beforeSave(){
        $value     = $this->getValue();
        	if ((!is_numeric($value) && !empty($value)) || $value < 0) {
        	    throw new Exception(Mage::helper('ves_productcarousel2')->__('Qty of products must be numeric.'));
        	}
        return $this;
    }

}

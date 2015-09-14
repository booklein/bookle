<?php
class Ves_Deals_Model_System_Config_Source_ListDealorder
{
	
    public function toOptionArray()
    {
        return array(
            array('value' => "name_desc", 'label'=>Mage::helper('adminhtml')->__('Name Descending')),
            array('value' => "name_asc", 'label'=>Mage::helper('adminhtml')->__('Name Ascending')),
            array('value' => "date_desc", 'label'=>Mage::helper('adminhtml')->__('Date added - Descending')),
            array('value' => "date_asc", 'label'=>Mage::helper('adminhtml')->__('Date added - Ascending')),
            array('value' => "price_desc", 'label'=>Mage::helper('adminhtml')->__('Price - Descending')),
            array('value' => "price_asc", 'label'=>Mage::helper('adminhtml')->__('Price - Ascending')),
        );
    }
}

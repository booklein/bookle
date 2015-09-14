<?php
class Ves_Deals_Model_System_Config_Source_ListRangedate
{
	
    public function toOptionArray()
    {
        return array(
            array('value' => "10", 'label'=>Mage::helper('adminhtml')->__('10 days')),
            array('value' => "15", 'label'=>Mage::helper('adminhtml')->__('15 days')),
            array('value' => "30", 'label'=>Mage::helper('adminhtml')->__('30 days')),
            array('value' => "45", 'label'=>Mage::helper('adminhtml')->__('45 days')),
            array('value' => "88", 'label'=>Mage::helper('adminhtml')->__('From Now To Future')),
            array('value' => "99", 'label'=>Mage::helper('adminhtml')->__('Specified Date')),
        );
    }
}

<?php
class Ves_Deals_Model_System_Config_Source_ListTheme
{
	public function toOptionArray()
    {
        return array(
            array('value' => "transparent", 'label'=>Mage::helper('adminhtml')->__('Transparent')),
            array('value' => "light", 'label'=>Mage::helper('adminhtml')->__('Light')),
            array('value' => "gray", 'label'=>Mage::helper('adminhtml')->__('Gray')),
            array('value' => "drark", 'label'=>Mage::helper('adminhtml')->__('Drark')),
        );
    }
}

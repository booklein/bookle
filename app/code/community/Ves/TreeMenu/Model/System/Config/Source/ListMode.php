<?php

class Ves_TreeMenu_Model_System_Config_Source_ListMode
{
	
    public function toOptionArray()
    {
        return array(
            array('value' => "all", 'label'=>Mage::helper('adminhtml')->__('Load all category of site')),
            array('value' => "auto", 'label'=>Mage::helper('adminhtml')->__('Auto load by current category'))
        );
    }
}

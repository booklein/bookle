<?php
class Ves_BlockBuilder_Block_Adminhtml_Blockbuilder_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $model = Mage::registry("block_data");
        $this->setForm($form);
        $customCssFieldset = $form->addFieldset("css_setting", array("legend" => Mage::helper("ves_blockbuilder")->__("Custom Css")));

        $customCssFieldset->addField('custom_css', 'textarea', array(
            'name'      => 'custom_css',
            'label'     => Mage::helper('cms')->__('Custom CSS'),
            'note' => Mage::helper('ves_blockbuilder')->__('Enter custom CSS code here. Your custom CSS will be outputted only on this particular page.'),
            'style'     => 'width:90%;height:24em;'
        ));

        
        $wrapperFieldset = $form->addFieldset("wrapper_setting", array("legend" => Mage::helper("ves_blockbuilder")->__("Wrapper For Page Builder")));

        $wrapperFieldset->addField('enable_wrapper', 'select', array(
            'label' => Mage::helper('ves_blockbuilder')->__('Enable Wrapper Block'),
            'options'   => array(
                '2' => Mage::helper('cms')->__('Disabled'),
                '1' => Mage::helper('cms')->__('Enabled')
            ),
            'name' => 'enable_wrapper',
            "class" => "form-control",
            "required" => false
        ));

        $wrapperFieldset->addField("wrapper_class", "text", array(
                "label" => Mage::helper("ves_blockbuilder")->__("Wrapper Class"),
                "name" => "wrapper_class",
                "class" => "form-control",
                "required" => false
        ));

        $wrapperFieldset = $form->addFieldset("widget_setting", array("legend" => Mage::helper("ves_blockbuilder")->__("Page Builder Widget Settings")));

        $wrapperFieldset->addField("template", "text", array(
                "label" => Mage::helper("ves_blockbuilder")->__("Custom Template"),
                'note' => Mage::helper('ves_blockbuilder')->__('Input custom module template file path. For example: ves/blockbuilder/row.phtml Empty for default'),
                "name" => "template",
                "class" => "form-control",
                "required" => false
        ));
        

        if (Mage::getSingleton("adminhtml/session")->getBlockData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getBlockData());
            Mage::getSingleton("adminhtml/session")->getBlockData(null);
        } elseif ($model) {
            $form->setValues($model->getData());
        }

        return parent::_prepareForm();
    }
}

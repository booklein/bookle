<?php
class Ves_Tempcp_Block_Adminhtml_Datasample_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("block_data", array("legend" => Mage::helper("ves_tempcp")->__("Theme Data Samples")));

        $fieldset->addField("title", "text", array(
                "label" => Mage::helper("ves_tempcp")->__("List "),
                "name" => "title",
                "class" => "form-control required-entry",
                "required" => true
            ));

        return parent::_prepareForm();
    }


    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return true;
    }
}

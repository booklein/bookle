<?php
class Ves_Tempcp_Block_Adminhtml_Datasample_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("tempcp_tabs");
        $this->setDestElementId("edit_form");
        $this->setTitle(Mage::helper("ves_tempcp")->__("Ves Theme Control Panel - Install Data Sample"));
        
    }

    protected function _beforeToHtml()
    {

        $this->addTab("form_section", array(
            "label" => Mage::helper("ves_tempcp")->__("Data Sample"),
            "title" => Mage::helper("ves_tempcp")->__("Data Sample"),
            "content" => $this->getLayout()->createBlock("ves_tempcp/adminhtml_datasample_tab_form")->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}

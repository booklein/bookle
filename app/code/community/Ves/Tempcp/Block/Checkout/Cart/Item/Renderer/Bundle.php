<?php
class Ves_Tempcp_Block_Checkout_Cart_Item_Renderer_Bundle extends Mage_Bundle_Block_Checkout_Cart_Item_Renderer
{
    protected function getEditItemHtml()
    {
    	$helper = Mage::helper("ves_tempcp/framework")->getFramework( );

		if(!$helper || !is_object($helper)) {

			$themeConfig = Mage::helper('ves_tempcp/theme')->getCurrentTheme();
			$helper = Mage::helper("ves_tempcp/framework")->initFramework( $themeName, $themeConfig );

		} else {

			$themeConfig = $helper->getConfig();
			
		}
		if($themeConfig->get("enable_cartedit")) {
	        $blockEdit = $this->getLayout()->createBlock('ves_tempcp/checkout_cart_item_renderer_edit_bundle')->setItem($this->getItem());
	        return $blockEdit->toHtml();
	    }
	    return;
    }
}
<?php
class Ves_Tempcp_Block_Checkout_Cart_Item_Renderer_Edit_Default extends Mage_Catalog_Block_Product_View_Options
{
    protected function _construct()
    {
        parent::_construct();
        if(isset($attributes['template']) && $attributes['template']) {
            $this->setTemplate($attributes['template']);
        } elseif($this->hasData("template")) {
            $this->setTemplate($this->getData('template'));
        } else {
            $template = 'venustheme/tempcp/cartedit/checkout/cart/item/edit/default.phtml';
            $this->setTemplate( $template );
        }

        $this->addOptionRenderer('text','catalog/product_view_options_type_text','catalog/product/view/options/type/text.phtml');
        $this->addOptionRenderer('file','catalog/product_view_options_type_file','catalog/product/view/options/type/file.phtml');
        $this->addOptionRenderer('select','catalog/product_view_options_type_select','catalog/product/view/options/type/select.phtml');
        $this->addOptionRenderer('date','catalog/product_view_options_type_date','catalog/product/view/options/type/date.phtml');
    }

    /**
     * Retrive product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        $product = Mage::getModel('catalog/product')->load($this->getItem()->getProductId());
        if (is_null($product->getTypeInstance(true)->getStoreFilter($product))) {
            $product->getTypeInstance(true)->setStoreFilter(Mage::app()->getStore(), $product);
        }

        // Prepare data
        $productHelper = Mage::helper('catalog/product');

        $buyRequest = $this->getItem()->getBuyRequest();
        if ($buyRequest) {
            $productHelper->prepareProductOptions($product, $buyRequest);
        }

        $product->setConfigureMode(true);

        return $product;
    }

    /**
     * Get option html block
     *
     * @param Mage_Catalog_Model_Product_Option $option
     */
    public function getOptionHtml(Mage_Catalog_Model_Product_Option $option)
    {
        $html = parent::getOptionHtml($option);
        $html = str_replace('opConfig.reloadPrice()','return;',$html);
        $html = str_replace('name="options[','name="cart['.$this->getItem()->getId().'][options][',$html);
        return $html;
    }
}
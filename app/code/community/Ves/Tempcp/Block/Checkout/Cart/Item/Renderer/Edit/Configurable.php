<?php
class Ves_Tempcp_Block_Checkout_Cart_Item_Renderer_Edit_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable
{
    protected function _construct()
    {
        parent::_construct();
        if(isset($attributes['template']) && $attributes['template']) {
            $this->setTemplate($attributes['template']);
        } elseif($this->hasData("template")) {
            $this->setTemplate($this->getData('template'));
        } else {
            $template = 'venustheme/tempcp/cartedit/checkout/cart/item/edit/configurable.phtml';
            $this->setTemplate( $template );
        }
  
    }

    /**
     * Retrive product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        $product = $this->getItem()->getProduct();
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

    protected function _getAdditionalConfig()
    {
        $config = array();
        $config['containerId'] = 'configurable-item-'.$this->getItem()->getId();
        return $config;
    }
}
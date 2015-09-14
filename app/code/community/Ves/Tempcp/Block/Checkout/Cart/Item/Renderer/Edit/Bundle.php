<?php
class Ves_Tempcp_Block_Checkout_Cart_Item_Renderer_Edit_Bundle extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle
{
    protected function _construct()
    {
        parent::_construct();
        if(isset($attributes['template']) && $attributes['template']) {
            $this->setTemplate($attributes['template']);
        } elseif($this->hasData("template")) {
            $this->setTemplate($this->getData('template'));
        } else {
            $template = 'venustheme/tempcp/cartedit/checkout/cart/item/edit/bundle.phtml';
            $this->setTemplate( $template );
        }

        $this->addRenderer('select','bundle/catalog_product_view_type_bundle_option_select');
        $this->addRenderer('multi','bundle/catalog_product_view_type_bundle_option_multi');
        $this->addRenderer('radio','bundle/catalog_product_view_type_bundle_option_radio');
        $this->addRenderer('checkbox','bundle/catalog_product_view_type_bundle_option_checkbox');
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
     * Get option html
     *
     * @param Mage_Catalog_Model_Product_Option $option
     * @return string
     */
    public function getOptionHtml($option)
    {
        if (!isset($this->_optionRenderers[$option->getType()])) {
            return $this->__('There is no defined renderer for "%s" option type.', $option->getType());
        }
        $html = $this->getLayout()->createBlock($this->_optionRenderers[$option->getType()])
            ->setOption($option)->setProduct($this->getProduct())->toHtml();
        $html = str_replace('bundle.changeSelection(this)','return;',$html);
        $html = str_replace('name="bundle_option[','name="cart['.$this->getItem()->getId().'][bundle_option][',$html);
        $html = str_replace('name="bundle_option_qty[','name="cart['.$this->getItem()->getId().'][bundle_option_qty][',$html);
        return $html;
    }
}
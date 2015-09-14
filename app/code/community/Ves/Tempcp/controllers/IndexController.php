<?php

require("Mage/Wishlist/controllers/IndexController.php");

class Ves_Tempcp_IndexController extends Mage_Wishlist_IndexController {

    public function minicartAction(){

    	$json = array();

        $json['html'] =  Mage::helper("ves_tempcp/framework")->getMinicartHtml();
        $json['summary_qty'] = Mage::getSingleton('checkout/cart')->getSummaryQty();
        $json['summary_qty'] = !empty($json['summary_qty'])?$json['summary_qty']:0;
        $json['subtotal'] = Mage::helper('ves_tempcp')->getCartSubtotal();
        
        echo Mage::helper('core')->jsonEncode( $json );
        return;
    }

    /**
     * Add wishlist item to shopping cart and remove from wishlist
     *
     * If Product has required options - item removed from wishlist and redirect
     * to product view page with message about needed defined required options
     */
    public function cartAction()
    {
    	$params = $this->getRequest()->getParams();
		if(isset($params['isAjax']) && $params['isAjax'] == 1){
			$response = array(); 
			if (!$this->_validateFormKey()) {
	            $response['status'] = 'ERROR';
				$response['message'] = $this->__('Some fields in form are required.'); 
	        }
	        $itemId = (int) $this->getRequest()->getParam('item');

	        /* @var $item Mage_Wishlist_Model_Item */
	        $item = Mage::getModel('wishlist/item')->load($itemId);
	        if (!$item->getId()) {
	           $response['status'] = 'ERROR';
			   $response['message'] = $this->__('Unable to find Product ID'); 
	        }
	        $wishlist = $this->_getWishlist($item->getWishlistId());
	        if (!$wishlist) {
	            $response['status'] = 'ERROR';
			    $response['message'] = $this->__('Empty wishlist'); 
	        }

	        // Set qty
	        $qty = $this->getRequest()->getParam('qty');
	        if (is_array($qty)) {
	            if (isset($qty[$itemId])) {
	                $qty = $qty[$itemId];
	            } else {
	                $qty = 1;
	            }
	        }
	        $qty = $this->_processLocalizedQty($qty);
	        if ($qty) {
	            $item->setQty($qty);
	        }

	        /* @var $session Mage_Wishlist_Model_Session */
	        $session    = Mage::getSingleton('wishlist/session');
	        $cart       = Mage::getSingleton('checkout/cart');

	        $redirectUrl = Mage::getUrl('*/*');

	        try {
	            $options = Mage::getModel('wishlist/item_option')->getCollection()
	                    ->addItemFilter(array($itemId));
	            $item->setOptions($options->getOptionsByItem($itemId));

	            $buyRequest = Mage::helper('catalog/product')->addParamsToBuyRequest(
	                $this->getRequest()->getParams(),
	                array('current_config' => $item->getBuyRequest())
	            );

	            $item->mergeBuyRequest($buyRequest);
	            if ($item->addToCart($cart, true)) {
	                $cart->save()->getQuote()->collectTotals();
	            }

	            $wishlist->save();
	            Mage::helper('wishlist')->calculate();

	            if (Mage::helper('checkout/cart')->getShouldRedirectToCart()) {
	                $redirectUrl = Mage::helper('checkout/cart')->getCartUrl();
	            } else if ($this->_getRefererUrl()) {
	                $redirectUrl = $this->_getRefererUrl();
	            }
	            Mage::helper('wishlist')->calculate();
	            $product = $item->getProduct();
	            $message = Mage::app()->getLayout()
				            ->createBlock("page/html")
				            ->assign("product", $product)
				            ->setTemplate('venustheme/tempcp/cart_success.phtml')
				            ->toHtml();

	            $response['status'] = 'SUCCESS';
				$response['message'] = $message; 
	        } catch (Mage_Core_Exception $e) {
	            if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
	            	$response['status'] = 'ERROR';
			    	$response['message'] = $this->__('This product(s) is currently out of stock');
	            } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
	                Mage::getSingleton('catalog/session')->addNotice($e->getMessage());
	                $redirectUrl = Mage::getUrl('*/*/configure/', array('id' => $item->getId()));
	                $response['status'] = 'ERROR';
			    	$response['message'] = $e->getMessage();
	            } else {
	                Mage::getSingleton('catalog/session')->addNotice($e->getMessage());
	                $redirectUrl = Mage::getUrl('*/*/configure/', array('id' => $item->getId()));
	                $response['status'] = 'ERROR';
			    	$response['message'] = $e->getMessage();
	            }
	        } catch (Exception $e) {
	            Mage::logException($e);
	            $session->addException($e, $this->__('Cannot add item to shopping cart'));
	            $response['status'] = 'ERROR';
			    $response['message'] = $this->__('Cannot add item to shopping cart');
	        }

	        Mage::helper('wishlist')->calculate();
	        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	        return ;
		} else {
			return parent::cartAction();
		}
    }

}

?>

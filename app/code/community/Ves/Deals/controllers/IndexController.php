<?php
class Ves_Deals_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
        $this->loadLayout();
        
        if($this->getRequest()->getParam('filter')){
        		Mage::register('filter_deal', $this->getRequest()->getParam('filter'));
        }
        $this->renderLayout();
	}
}
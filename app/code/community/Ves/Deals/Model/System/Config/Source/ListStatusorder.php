<?php
class Ves_Deals_Model_System_Config_Source_ListStatusorder {

    public function toOptionArray() {
           
        $orders = Mage::getModel('sales/order')->getCollection()
            //->addFieldToFilter('status', 'complete')
            ->addAttributeToSelect('customer_email')
            ->addAttributeToSelect('status')
            ;
        $arr = array();
        foreach ($orders as $order) {
            //$email = $order->getCustomerEmail();
           // echo $order->getId() . ": '" . $order->getStatus() . "', " . $email . "\n";
            $tmp = array();
                $tmp["value"] = $order->getStatus();
                $tmp["label"] = $order->getStatus();
                $arr[] = $tmp;
        }

        /*$orders = Mage::getModel('sales/order');
        $tree = $orders->getTreeModel();
        $tree->load();
        $ids = $tree->getCollection()->getAllIds();
        $arr = array();
        if ($ids) {
            foreach ($ids as $id) {
                $cat = Mage::getModel('sales/order');
                $cat->load($id);
                $tmp = array();
                $tmp["value"] = $id;
                $tmp["label"] = $cat->getStatus();
                $arr[] = $tmp;
            }
        }*/
        return $arr;
    }

}
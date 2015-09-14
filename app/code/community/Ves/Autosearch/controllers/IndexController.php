<?php
/******************************************************
 * @package Ves Autosearch module for Magento 1.4.x.x and Magento 1.9.x.x
 * @version 1.0.0.1
 * @author http://landofcoder.com
 * @copyright	Copyright (C) December 2010 LandOfCoder.com <@emai:landofcoder@gmail.com>.All rights reserved.
 * @license		GNU General Public License version 2
*******************************************************/
class Ves_Autosearch_IndexController extends Mage_Core_Controller_Front_Action
{
  public function indexAction()
  {
    $this->loadLayout();     
    $this->renderLayout();
  }

  public function ajaxgetproductAction()
  {
     $json = array();

     $limit = isset($_POST['limit'])?$this->getRequest()->getPost('limit'):Mage::getStoreConfig("ves_autosearch/ves_autosearch/limit");
     $limit = (int)$limit;

     $thumbWidth = isset($_POST['thumb_width'])?$this->getRequest()->getPost('thumb_width'):Mage::getStoreConfig("ves_autosearch/ves_autosearch/thumbWidth");

     $thumbHeight = isset($_POST['thumb_height'])?$this->getRequest()->getPost('thumb_height'):Mage::getStoreConfig("ves_autosearch/ves_autosearch/thumbHeight");


		  $category_id = $this->getRequest()->getPost('filter_category_id'); // if you know static category then enter number

		  $searchstring = $this->getRequest()->getPost('filter_name');

      $storeId  = Mage::app()->getStore()->getId();
      
      $query = Mage::helper('ves_autosearch')->getQuery();
      /* @var $query Mage_CatalogSearch_Model_Query */
      $query->setStoreId(Mage::app()->getStore()->getId());

      if ($query->getQueryText() != '') {
            if (Mage::helper('ves_autosearch')->isMinQueryLength()) {
                $query->setId(0)
                    ->setIsActive(1)
                    ->setIsProcessed(1);
            }
            else {
                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity()+1);
                }
                else {
                    $query->setPopularity(1);
                }

                $query->prepare();
            }
            if (!Mage::helper('ves_autosearch')->isMinQueryLength()) {

                $autosearch = Mage::getModel('ves_autosearch/search');

                $collection = $autosearch->getResultSearchCollection($searchstring, $category_id, $storeId);

                $total = $collection->getSize(); // get total result

                $collection->setPage(1, $limit);
                //===========================================
                if(!empty($collection))
                {
                  foreach ($collection as $_product){

                    $item_html = Mage::app()->getLayout()
                                ->createBlock("ves_autosearch/item")
                                ->assign("product", $_product)
                                ->toHtml();


                    $json[] = array(
                     'total'     => $total,
                     'product_id' => $_product->getId(),
                     'name'       => strip_tags(html_entity_decode($_product->getName(), ENT_QUOTES, 'UTF-8')), 
                     'image'       => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product/'.$_product->getData('small_image'),
                     'link'      => $_product->getProductUrl(),
                     'price'      => $_product->getPrice(),
                     'html'       => $item_html
                     );        
                  }

                  if (!empty($json)) {
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($json));

                  }else{
                   $json[] = array(
                    'total'      => 0,
                    'product_id' => 0,
                    'name'       => '',  
                    'image'      => '',
                    'link'       => '',
                    'price'      => 0,
                    'html'       => 'No products exists',
                    );       
                   $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($json));
                   //echo 'No products exists1';
                 }
                 
               } else {
                $json[] = array(
                  'total'      => 0,
                  'product_id' => 0,
                  'name'       => '',  
                  'image'      => '',
                  'link'       => '',
                  'price'      => 0,
                  'html'       => 'No products exists',
                  );       
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($json));
                //echo 'No products exists2';
              }
          }
          Mage::helper('ves_autosearch')->checkNotes();
          if (!Mage::helper('ves_autosearch')->isMinQueryLength()) {
              $query->save();
          }
      }
      if(!$json) {
        $json[] = array(
              'total'      => 0,
              'product_id' => 0,
              'name'       => '',  
              'image'      => '',
              'link'       => '',
              'price'      => 0,
              'html'       => 'No products exists',
              );       
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($json));
      }
  }

  protected function _getFlatTableName($store = null)
  {
        if (is_null($store)) {
            $store = Mage::app()->getStore()->getId();
        }
        return $this->getTable('catalog/product_flat') . '_' . $store;
  }

}
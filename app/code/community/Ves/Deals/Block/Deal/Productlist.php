<?php

class Ves_Deals_Block_Deal_Productlist extends Mage_Catalog_Block_Product_List
{

    /**
     * Contructor
     */
    public function __construct($attributes = array())
    {

        $helper =  Mage::helper('ves_deals/data');

        $this->_show = $this->getConfig("show");

        if(!$this->_show) return;
        /*End init meida files*/
        $mediaHelper =  Mage::helper('ves_deals/media');
        $mediaHelper->addMediaFile("skin_css", "ves_deals/style.css" );
        $mediaHelper->addMediaFile("js", "ves_deals/countdown.js" );
        
        parent::__construct();      
    }

    public function getEffectConfig( $key ){
        return $this->getConfig( $key, "effect_setting" );
    }

    public function getToDayDealsConfig( $key ){
        return $this->getConfig( $key, "today_deals_setting" );
    }
    /**
     * get value of the extension's configuration
     *
     * @return string
     */
    function getConfig( $key, $panel='ves_deals' ){
        if(isset($this->_config[$key])) {
            return $this->_config[$key];
        } else {
            return Mage::getStoreConfig("ves_deals/$panel/$key");
        }
    }
    protected function _prepareLayout()
    {
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $title = $this->getConfig("page_title", "deals_setting");
        if ($breadcrumbs) {
            $title = $this->getConfig("page_title", "deals_setting");

            $breadcrumbs->addCrumb('home', array( 'label' => $this->__('Home'),  'title' => $this->__('Go to Home Page'), 'link'  => Mage::getBaseUrl() ))->addCrumb('deals', array(
                'label' => $this->__("Deals"),
                'title' => $this->__("Deals"),
                'link'  => Mage::helper("ves_deals")->getFilterUrl(0)
                ))
            ->addCrumb('item', array(
                'label' => $title,
                'title' => $title,
                ));
        }
        $this->getLayout()->getBlock('head')->setTitle($title);
        return parent::_prepareLayout();
    }

    public function getFilterdeal() {
        return Mage::registry('filter_deal');
    }

    protected function _getProductCollection()    {
        if (is_null($this->_productCollection)) {
            $products = null;
            $todayDate          = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
            $time_start_date    = strtotime($todayDate);
            $start_date         = date('Y-m-d',strtotime($todayDate));
            $end_date           = date('Y-m-d', strtotime("+1 days", $time_start_date));
            $filter_date_start  = $this->getToDayDealsConfig('date_start');
            $filter_date_start  = trim($filter_date_start);
            $filter_to_date     = $this->getToDayDealsConfig('to_date');
            $filter_to_date     = trim($filter_to_date);

            $storeId        = Mage::app()->getStore()->getId();
            $cateids        = $this->getModuleConfig('catagories');
            $filter_deal    = $this->getFilterdeal();
            if(empty($filter_deal))
                $filter_deal = 1;

            if($cateids) {
                $productIds = $this->getProductByCategory();
                $products   = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('*')
                ->setStoreId($storeId)
                ->addStoreFilter($storeId)
                ->addIdFilter($productIds);
                if($filter_deal == 1) { //today 
                    $products->addAttributeToFilter(array(
                        array(
                                    'attribute' =>'special_to_date',
                                        'from' =>date('Y-m-d G:i:s', strtotime($filter_date_start)),
                                        'to' => date('Y-m-d G:i:s', strtotime($filter_to_date)),
                                        'date' => true,
                                 ),
                        array(
                                    'attribute' =>'special_to_date',
                                    'gteq' =>date('Y-m-d G:i:s', strtotime($filter_to_date)),
                                        'date' => true,
                                    ),
                        array(
                                    'attribute' =>'special_to_date',
                                    'gteq' =>date('Y-m-d G:i:s', strtotime($filter_date_start)),
                                        'date' => true,
                                    ),
                        ));
                } elseif($filter_deal == 2) { //available
                    $products->addFieldToFilter('special_to_date',array('gteq'=>$todayDate));
                } elseif($filter_deal == 3) { //pass
                    $products->addFieldToFilter('special_to_date',array('lt'=>$start_date));
                }

                $products->addAttributeToSort('special_to_date','desc');


            }else{
                $products = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('*')
                ->setStoreId($storeId)
                ->addStoreFilter($storeId);

                 if($filter_deal == 1) { //today
                    $products->addAttributeToFilter(array(
                        array(
                                    'attribute' =>'special_to_date',
                                        'from' =>date('Y-m-d G:i:s', strtotime($filter_date_start)),
                                        'to' => date('Y-m-d G:i:s', strtotime($filter_to_date)),
                                        'date' => true,
                                 ),
                        array(
                                    'attribute' =>'special_to_date',
                                    'gteq' =>date('Y-m-d G:i:s', strtotime($filter_to_date)),
                                        'date' => true,
                                    ),
                        array(
                                    'attribute' =>'special_to_date',
                                    'gteq' =>date('Y-m-d G:i:s', strtotime($filter_date_start)),
                                        'date' => true,
                                    ),
                        ));
                } elseif($filter_deal == 2) { //available
                    $products->addFieldToFilter('special_to_date',array('gteq'=>$todayDate));
                }elseif($filter_deal == 3) { //pass
                    $products->addFieldToFilter('special_to_date',array('lt'=>$start_date));
                }
                $products->addAttributeToSort('special_to_date','desc');
            }
            $this->_productCollection = $products;
            Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($this->_productCollection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($this->_productCollection);

        }

        return $this->_productCollection;
    }

    public function getLoadedProductCollection() {
        return $this->_getProductCollection();
    }


    function inArray($source, $target) {
        for($i = 0; $i < sizeof ( $source ); $i ++) {
            if (in_array ( $source [$i], $target )) {
                return true;
            }
        }
    }

    function getProductByCategory(){
        $return = array(); 
        $pids = array();
        $catsid = $this->getModuleConfig('catagories');
        $products = Mage::getResourceModel ( 'catalog/product_collection' );

        foreach ($products->getItems() as $key => $_product){
            $arr_categoryids[$key] = $_product->getCategoryIds();
            
            if($catsid){    
                if(stristr($catsid, ',') === FALSE) {
                    $arr_catsid[$key] =  array(0 => $catsid);
                }else{
                    $arr_catsid[$key] = explode(",", $catsid);
                }
                
                $return[$key] = $this->inArray($arr_catsid[$key], $arr_categoryids[$key]);
            }
        }
        
        foreach ($return as $k => $v){ 
            if($v==1) $pids[] = $k;
        }    
        
        return $pids;   
    }
}

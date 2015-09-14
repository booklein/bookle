<?php

class Ves_Autosearch_Model_Search extends Mage_Core_Model_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'autosearch_search';

    /**
     * Event object key name
     *
     * @var string
     */
    protected $_eventObject = 'autosearch_search';

    const CACHE_TAG                     = 'VES_AUTOSEARCH';
    const CACHE_WIDGET_TAG              = 'VES_AUTOSEARCH_WIDGET';
    const XML_PATH_MIN_QUERY_LENGTH     = 'catalog/search/min_query_length';
    const XML_PATH_MAX_QUERY_LENGTH     = 'catalog/search/max_query_length';
    const XML_PATH_MAX_QUERY_WORDS      = 'catalog/search/max_query_words';

    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('ves_autosearch/search');
    }

    /**
     * Retrieve search collection
     *
     * @return Mage_CatalogSearch_Model_Resource_Search_Collection
     */
    public function getSearchCollection()
    {
        return Mage::getResourceModel('ves_autosearch/search_collection');
    }

    public function getResultSearchCollection($searchstring = 0, $category_id = 0, $storeId = 0) {
        if(Mage::helper('catalog/product_flat')->isEnabled()){//Check product flat enable
                
                $collection = $this->getSearchCollection()
                                    ->addAttributeToSelect('*')
                                    ->addSearchFilter($searchstring)
                                    ->addMinimalPrice()
                                    ->addUrlRewrite()
                                    ->addTaxPercents()
                                    ->setStoreId($storeId)
                                    ->addStoreFilter();

                Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
                Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);

                if($category_id) {
                  $collection->addCategoryFilter(Mage::getModel('catalog/category')->load($category_id)); //category filter
                }

            } else { //If product flat disable
                $collection = $this->getSearchCollection();

                $collection->addAttributeToSelect('*'); //add product attribute to be fetched
                $collection->addAttributeToFilter('status',1); //only enabled product

                $collection->addSearchFilter($searchstring)
                            ->setStoreId($storeId)
                            ->addStoreFilter($storeId)
                            ->addMinimalPrice()
                            ->addTaxPercents();

                Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
                Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);

                if($category_id) {
                  $collection->addCategoryFilter(Mage::getModel('catalog/category')->load($category_id)); //category filter
                }
                
            }
        return $collection;
    }

}
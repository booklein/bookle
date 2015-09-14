<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Blog Extension
 *
 * @category   Ves
 * @package    Ves_Blog
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Blog_Block_Adminhtml_Category_Exportgrid extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct() {

        parent::__construct();
        $this->setId('categoryGrid');
        $this->setDefaultSort('date_from');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);

    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('ves_blog/category')->getCollection();
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        foreach ($collection as $_category) {
            $results = $query = '';
            $query = 'SELECT store_id FROM ' . $resource->getTableName('ves_blog/category_store').' WHERE category_id = '.$_category->getCategoryId();
            $results = $readConnection->fetchCol($query);
            $_category->setData('stores', implode('-', $results));
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('category_id', array(
            'header'    => Mage::helper('ves_blog')->__('category_id'),
            'index'     => 'category_id',
            ));

        $this->addColumn('description', array(
            'header'    => Mage::helper('ves_blog')->__('description'),
            'index'     => 'description'
            ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('ves_blog')->__('title'),
            'index'     => 'title'
            ));

        $this->addColumn('layout', array(
            'header'    => Mage::helper('ves_blog')->__('layout'),
            'index'     => 'layout'
            ));

        $this->addColumn('identifier', array(
            'header'    => Mage::helper('ves_blog')->__('identifier'),
            'index'     => 'identifier'
            ));

        $this->addColumn('parent_id', array(
            'header'    => Mage::helper('ves_blog')->__('parent_id'),
            'index'     => 'parent_id'
            ));

        $this->addColumn('file', array(
            'header'    => Mage::helper('ves_blog')->__('file'),
            'index'     => 'file'
            ));

        $this->addColumn('meta_keywords', array(
            'header'    => Mage::helper('ves_blog')->__('meta_keywords'),
            'index'     => 'meta_keywords'
            ));

        $this->addColumn('meta_description', array(
            'header'    => Mage::helper('ves_blog')->__('meta_description'),
            'index'     => 'meta_description'
            ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('ves_blog')->__('is_active'),
            'index'     => 'is_active'
            ));

        $this->addColumn('position', array(
            'header'    => Mage::helper('ves_blog')->__('position'),
            'index'     => 'position'
            ));

        $this->addColumn('stores', array(
            'header'    => Mage::helper('ves_blog')->__('stores'),
            'index'     => 'stores'
            ));

        return parent::_prepareColumns();
    }

}
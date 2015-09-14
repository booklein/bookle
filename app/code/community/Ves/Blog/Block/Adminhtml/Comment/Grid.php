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
class Ves_Blog_Block_Adminhtml_Comment_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct() {

        parent::__construct();

        $this->setId('commentGrid');
        $this->setDefaultSort('date_from');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);

    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('ves_blog/comment')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('comment_id', array(
            'header'    => Mage::helper('ves_blog')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'comment_id',
            ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id',
                array (
                    'header' => Mage::helper('cms')->__('Store view'),
                    'index' => 'store_id',
                    'type' => 'store',
                    'store_all' => true,
                    'store_view' => true,
                    'sortable' => false,
                    'filter_condition_callback' => array (
                        $this,
                        '_filterStoreCondition' ) ));
        }

        $this->addColumn('comment', array(
            'header'    => Mage::helper('ves_blog')->__('Comment'),
            'align'     =>'left',
            'index'     => 'comment',
            ));
        $this->addColumn('post_title', array(
            'header'    => Mage::helper('ves_blog')->__('Post Title'),
            'align'     =>'left',
            'index'     => 'post_title',
            'width'     => '250px',
            ));
        $this->addColumn('created', array(
            'header'    => Mage::helper('ves_blog')->__('Created Time'),
            'align'     =>'left',
            'index'     => 'created',
            'width'     => '150px',
            ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('ves_blog')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'width'     => '50px',
            'options'   => array(
                1 => Mage::helper('ves_blog')->__('Enabled'),
                2 => Mage::helper('ves_blog')->__('Disabled'),
                ),
            ));
        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        return parent::_prepareColumns();
    }

    /**
     * Helper function to do after load modifications
     *
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * Helper function to add store filter condition
     *
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('comment_id');
        $this->getMassactionBlock()->setFormFieldName('comment');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => Mage::helper('ves_blog')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('ves_blog')->__('Are you sure?')
            ));

        $statuses = array(
            1 => Mage::helper('ves_blog')->__('Enabled'),
            0 => Mage::helper('ves_blog')->__('Disabled')
            );
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('ves_blog')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('ves_blog')->__('Status'),
                    'values' => $statuses
                    )
                )
            ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
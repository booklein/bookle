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
class Ves_Blog_Block_Adminhtml_Category extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {


        $this->_controller = 'adminhtml_category';
        $this->_blockGroup = 'ves_blog';
        $this->_headerText = Mage::helper('ves_blog')->__('Category Manager');

        parent::__construct();
        $this->setTemplate('ves_blog/category.phtml');
    }

    protected function _prepareLayout() {

        $this->setChild('add_new_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('ves_blog')->__('Add Record'),
                'onclick'   => "setLocation('".$this->getUrl('*/*/add')."')",
                'class'   => 'add'
                ))
            );

        $this->setChild('import_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('ves_blog')->__('Import CSV'),
                'onclick'   => "setLocation('".$this->getUrl('*/*/uploadCsv')."')",
                'class'   => 'add'
                ))
            );

        $this->setChild('mass_rewrite_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('ves_blog')->__('Mass Generate Rewrite URLs'),
                'onclick'   => "setLocation('".$this->getUrl('*/*/massRewrite')."')",
                'class'   => 'mass'
                ))
            );

        $this->setChild('grid', $this->getLayout()->createBlock('ves_blog/adminhtml_category_grid', 'category.grid'));
        return parent::_prepareLayout();
    }

    public function getImportButtonHtml() {
        return $this->getChildHtml('import_button');
    }

    public function getAddNewButtonHtml() {
        return $this->getChildHtml('add_new_button');
    }

    public function getMassRewriteCatButtonHtml(){
        return $this->getChildHtml('mass_rewrite_button');
    }
    public function getGridHtml() {
        return $this->getChildHtml('grid');
    }
}
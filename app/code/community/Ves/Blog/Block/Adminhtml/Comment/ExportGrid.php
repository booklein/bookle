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
class Ves_Blog_Block_Adminhtml_Comment_Exportgrid extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct() {
        parent::__construct();
        $this->setId('categoryGrid');
        $this->setDefaultSort('date_from');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('ves_blog/comment')->getCollection();
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        foreach ($collection as $_comment) {
            $results = $query = '';
            $query = 'SELECT store_id FROM ' . $resource->getTableName('ves_blog/comment_store').' WHERE comment_id = '.$_comment->getCommentId();
            $results = $readConnection->fetchCol($query);
            $_comment->setData('stores', implode('-', $results));
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('comment_id', array(
            'header'    => Mage::helper('ves_blog')->__('comment_id'),
            'index'     => 'comment_id',
            ));

        $this->addColumn('post_id', array(
            'header'    => Mage::helper('ves_blog')->__('post_id'),
            'index'     => 'post_id'
            ));

        $this->addColumn('comment', array(
            'header'    => Mage::helper('ves_blog')->__('comment'),
            'index'     => 'comment'
            ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('ves_blog')->__('is_active'),
            'index'     => 'is_active'
            ));

        $this->addColumn('created', array(
            'header'    => Mage::helper('ves_blog')->__('created'),
            'index'     => 'created'
            ));

        $this->addColumn('user', array(
            'header'    => Mage::helper('ves_blog')->__('user'),
            'index'     => 'user'
            ));

        $this->addColumn('email', array(
            'header'    => Mage::helper('ves_blog')->__('email'),
            'index'     => 'email'
            ));

        $this->addColumn('stores', array(
            'header'    => Mage::helper('ves_blog')->__('stores'),
            'index'     => 'stores'
            ));

        return parent::_prepareColumns();
    }

}
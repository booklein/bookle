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
class Ves_Blog_Block_Adminhtml_Comment_Edit_Tab_Param extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$_model = Mage::registry('comment_data');
		$form = new Varien_Data_Form();
		$this->setForm($form);

		$fieldset = $form->addFieldset('comment_params', array('legend'=>Mage::helper('ves_blog')->__('Parameter')));

		$fieldset->addField('template', 'select', array(
			'label'     => Mage::helper('ves_blog')->__('Template'),
			'name'      => 'param[template]',
			'values'    => array( 0=> $this->__("No"), 1=> $this->__("Yes") )
			));

		$fieldset->addField('show_childrent', 'select', array(
			'label'     => Mage::helper('ves_blog')->__('Show Childrent'),
			'name'      => 'param[show_childrent]',
			'values'    => array( 0=> $this->__("No"), 1=> $this->__("Yes") )
			));

		$fieldset->addField('primary_cols', 'text', array(
			'label'     => Mage::helper('ves_blog')->__('Show Childrent'),
			'name'      => 'param[primary_cols]',
			'default'   => '2'
			));

		if ( Mage::getSingleton('adminhtml/session')->getCommentData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getCommentData());
			Mage::getSingleton('adminhtml/session')->getCommentData(null);
		} elseif ( Mage::registry('comment_data') ) {
			$form->setValues(Mage::registry('comment_data')->getData());
		}

		return parent::_prepareForm();
	}

	public function getParentToOptionArray() {
		$catCollection = Mage::getModel('ves_blog/comment')
		->getCollection();
		$id = Mage::registry('comment_data')->getId();
		if($id) {
			$catCollection->addFieldToFilter('comment_id', array('neq' => $id));
		}
		$option = array();
		$option[] = array( 'value' => 0,
			'label' => 'Top Level');
		foreach($catCollection as $cat) {
			$option[] = array( 'value' => $cat->getId(),
				'label' => $cat->getTitle() );
		}
		return $option;
	}
}

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
class Ves_Blog_Block_Adminhtml_Comment_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$_model = Mage::registry('comment_data');
		$form = new Varien_Data_Form();
		$this->setForm($form);

		$fieldset = $form->addFieldset('comment_form', array('legend'=>Mage::helper('ves_blog')->__('General Information')));

		$fieldset->addField('is_active', 'select', array(
			'label'     => Mage::helper('ves_blog')->__('Is Active'),
			'name'      => 'is_active',
			'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            //'value'     => $_model->getIsActive()
			));
		$fieldset->addField('user', 'text', array(
			'label'     => Mage::helper('ves_blog')->__('User'),
			'name'      => 'user',

            //'value'     => $_model->getIsActive()
			));
		$fieldset->addField('email', 'text', array(
			'label'     => Mage::helper('ves_blog')->__('Email'),
			'name'      => 'user',

            //'value'     => $_model->getIsActive()
			));

		try{
			$config = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
				array(
					'add_widgets' => false,
					'add_variables' => false,
					)
				);
			if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
				$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
			}

			$config->setData(Mage::helper('ves_blog')->recursiveReplace(
				'/ves_blog/',
				'/'.(string)Mage::app()->getConfig()->getNode('admin/routers/adminhtml/args/frontName').'/',
				$config->getData()
				)
			);

		}
		catch (Exception $ex){
			$config = null;
		}
		$fieldset->addField('comment', 'editor', array(
			'label'     => Mage::helper('ves_blog')->__('Comment'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'comment',
			'style'     => 'width:600px;height:300px;',
			'wysiwyg'   => false,
			'config'    =>  $config,
			));

		$fieldset->addField('store_id', 'multiselect', array(
			'name' => 'stores[]',
			'label' => Mage::helper('ves_blog')->__('Store View'),
			'title' => Mage::helper('ves_blog')->__('Store View'),
			'required' => true,
			'values' => Mage::getSingleton('adminhtml/system_store')
			->getStoreValuesForForm(false, true),
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

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
class Ves_Blog_Block_Adminhtml_Post_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$_model = Mage::registry('post_data');
		$form = new Varien_Data_Form();
		$this->setForm($form);

		$fieldset = $form->addFieldset('post_meta', array('legend'=>Mage::helper('ves_blog')->__('Meta Information')));

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
		$fieldset->addField('meta_keywords', 'editor', array(
			'label'     => Mage::helper('ves_blog')->__('Meta Keywords'),
			'class'     => '',
			'required'  => false,
			'name'      => 'meta_keywords',
			'style'     => 'width:600px;height:100px;',
			'wysiwyg'   => false

			));
		$fieldset->addField('meta_description', 'editor', array(
			'label'     => Mage::helper('ves_blog')->__('Meta Description'),
			'class'     => '',
			'required'  => false,
			'name'      => 'meta_description',
			'style'     => 'width:600px;height:100px;',
			'wysiwyg'   => false
			));


		if ( Mage::getSingleton('adminhtml/session')->getPostData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getPostData());
			Mage::getSingleton('adminhtml/session')->getPostData(null);
		} elseif ( Mage::registry('post_data') ) {
			$form->setValues(Mage::registry('post_data')->getData());
		}

		return parent::_prepareForm();
	}


}
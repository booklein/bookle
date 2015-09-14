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
class Ves_Blog_Block_Adminhtml_Post_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
    {
        $_model = Mage::registry('post_data');
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $storeId = Mage::app()->getStore(true)->getId();
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array(
                'wysiwyg'                   => true,
                'add_widgets' => false,
                'add_variables' => false,
                'add_images' => true,
                'encode_directives'             => true,
                'store_id'                  => $storeId,
                'add_directives'            => true,
                'directives_url'                => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_wysiwyg/directive'),
                'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
                'files_browser_window_width' => (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_width'),
                'files_browser_window_height'=> (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_height')
                )
            );


        $fieldset = $form->addFieldset('post_meta', array('legend'=>Mage::helper('ves_blog')->__('Meta Information')));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('ves_blog')->__('Is Active'),
            'name'      => 'is_active',
            'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            ));
        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('ves_blog')->__('Title'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'title',
            ));
        $fieldset->addField('identifier', 'text', array(
            'label'     => Mage::helper('ves_blog')->__('Identifier'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'identifier',
            ));
        $fieldset->addField('category_id', 'select', array(
            'label'     => Mage::helper('ves_blog')->__('Category'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'category_id',
            'values'   => Mage::helper('ves_blog')->getCategoriesList()
            ));

        $fieldset->addField('video_type', 'select', array(
            'label'     => Mage::helper('ves_blog')->__('Video Type'),
            'name'      => 'video_type',
            'class'     => '',
            'required'  => false,
            'values' => array(
                ''=>'Please Select..',
                '1' => array(
                    'value'=> 'youtube',
                    'label' => 'Youtube'
                    ),
                '2' => array(
                    'value'=> 'vimeo',
                    'label' => 'Vimeo'
                    ),
                ),
            'note' => Mage::helper('ves_blog')->__('<small>Type Youtube Or Vimeo</small>'),
            'tabindex' => 1
            ));

        $fieldset->addField('video_id', 'text', array(
            'label'     => Mage::helper('ves_blog')->__('Video ID'),
            'name'      => 'video_id',
            'note' => Mage::helper('ves_blog')->__('For Example ID: https://www.youtube.com/watch?v=BBvsB5PcitQ  => VideoID = BBvsB5PcitQ')
            ));
        $fieldset->addField('tags', 'text', array(
            'label'     => Mage::helper('ves_blog')->__('Tags'),
            'class'     => '',
            'required'  => false,
            'name'      => 'tags',
            ));
        $fieldset->addField('file', 'image', array(
            'label'     => Mage::helper('ves_blog')->__('Image'),
            'class'     => '',
            'required'  => false,
            'name'      => 'file',
            ));

        $dateFormatIso = $this->escDates();

        $fieldset->addField('created', 'date', array(
          'name'   => 'created',
          'label'  => Mage::helper('ves_blog')->__('Created Date'),
          'title'  => Mage::helper('ves_blog')->__('Created Date'),
          'note' => Mage::helper('ves_blog')->__('Empty to get Today Date Time'),
          'image'  => $this->getSkinUrl('images/grid-cal.gif'),
          'input_format' => $dateFormatIso,
          'format'       => $dateFormatIso,
          'time' => true
          ));

        $fieldset->addField('updated', 'label', array(
          'name'   => 'updated',
          'label'  => Mage::helper('ves_blog')->__('Modified Date'),
          'title'  => Mage::helper('ves_blog')->__('Modified Date')
          ));

        $fieldset->addField('position', 'text', array(
            'label'     => Mage::helper('ves_blog')->__('Position'),
            'class'     => '',
            'required'  => false,
            'name'      => 'position'
            ));
        $fieldset->addField('hits', 'text', array(
            'label'     => Mage::helper('ves_blog')->__('Hits'),
            'class'     => '',
            'required'  => false,
            'name'      => 'hits'
            ));
        $fieldset->addField('description', 'textarea', array(
            'label'     => Mage::helper('ves_blog')->__('Description'),
            'class'     => '',
            'required'  => false,
            'name'      => 'description',
            'style'     => 'width:600px;height:200px;',
            'wysiwyg'   => true,
            'config'    => $config
            ));
        $fieldset->addField('detail_content', 'textarea', array(
            'label'     => Mage::helper('ves_blog')->__('Content'),
            'class'     => '',
            'required'  => false,
            'name'      => 'detail_content',
            'style'     => 'width:600px;height:300px;',
            'wysiwyg'   => true,
            'config'   => $config
            ));

        $fieldset->addField('store_id', 'multiselect', array(
            'name' => 'stores[]',
            'label' => Mage::helper('ves_blog')->__('Store View'),
            'title' => Mage::helper('ves_blog')->__('Store View'),
            'required' => true,
            'values' => Mage::getSingleton('adminhtml/system_store')
            ->getStoreValuesForForm(false, true),
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
    private function escDates()s {
        return 'yyyy-MM-dd HH:mm:ss';
    }
}
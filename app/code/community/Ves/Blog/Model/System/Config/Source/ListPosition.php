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
class Ves_Blog_Model_System_Config_Source_ListPosition
{
   public function toOptionArray()
   {
    return array(
       array('value' => "root", 'label'=>Mage::helper('adminhtml')->__('Root')),
       array('value' => "content", 'label'=>Mage::helper('adminhtml')->__('Content')),
       array('value' => "left", 'label'=>Mage::helper('adminhtml')->__('Left')),
       array('value' => "right", 'label'=>Mage::helper('adminhtml')->__('Right')),
       array('value' => "top.menu", 'label'=>Mage::helper('adminhtml')->__('Top Menu')),
       array('value' => "product.info", 'label'=>Mage::helper('adminhtml')->__('Product Info')),
       array('value' => "top.links", 'label'=>Mage::helper('adminhtml')->__('Top Links')),
       array('value' => "my.account.wrapper", 'label'=>Mage::helper('adminhtml')->__('My Account Wrapper')),
       array('value' => "footer", 'label'=>Mage::helper('adminhtml')->__('Footer')),
       array('value' => "footer_links", 'label'=>Mage::helper('adminhtml')->__('Footer Links'))
       );
}
}
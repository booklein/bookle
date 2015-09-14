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
class Ves_Blog_Model_System_Config_Source_ListManufacturers {

    public function toOptionArray() {
        $manufacturers = $this->getAllManu();
        $arr = array();
        if ($manufacturers) {
            foreach ($manufacturers as $manu) {
                $tmp = array();
                $tmp["value"] = $manu['value'];
                $tmp["label"] = $manu['label'];
                $arr[] = $tmp;
            }
        }
        return $arr;
    }

    public function getAllManu()
    {
      $product = Mage::getModel('catalog/product');
      $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
      ->setEntityTypeFilter($product->getResource()->getTypeId())
      ->addFieldToFilter('attribute_code', 'computer_manufacturers');
      $attribute = $attributes->getFirstItem()->setEntity($product->getResource());
      $manufacturers = $attribute->getSource()->getAllOptions(false);
      return $manufacturers;
  }
}
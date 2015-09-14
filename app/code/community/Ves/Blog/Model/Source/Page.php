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
class Ves_Blog_Model_Config_Source_Page
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
      $_collection = Mage::getSingleton('cms/page')->getCollection()
      ->addFieldToFilter('is_active', 1);

      $_result = array();
      foreach ($_collection as $item) {
        $data = array(
          'value' => $item->getData('page_id'),
          'label' => $item->getData('title'));
        $_result[] = $data;
      }
      return $_result;
    }
  }
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
class Ves_Blog_Model_Comment extends Mage_Core_Model_Abstract
{
  protected function _construct()
  {
   $this->_init('ves_blog/comment');

 }
 public function getURL(){
  if($isSecure = Mage::app()->getStore()->isCurrentlySecure()) {
    $base_url = Mage::getBaseUrl( Mage_Core_Model_Store::URL_TYPE_WEB, true );
  } else {
    $base_url = Mage::getBaseUrl();
  }
  return $base_url.Mage::getModel('core/url_rewrite')->loadByIdPath('venusblog/post/'.$this->getId())->getRequestPath();
}

}
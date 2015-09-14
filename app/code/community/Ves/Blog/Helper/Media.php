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
class Ves_Blog_Helper_Media extends Mage_Core_Helper_Abstract {
	/**
	 *
	 * Add media file ( js, css ) ...
	 * @param $type string media type (js, skin_css)
	 * @param $source string source path
	 * @param $before boolean true/false
	 * @param $params mix
	 * @param $if string
	 * @param $cond string
	 */
	function addMediaFile($type = "", $source = "", $before = false, $params=null, $if="", $cond="" ){
		$_head = Mage::getSingleton('core/layout')->getBlock( 'head');
		if(is_object($_head) && !empty($source)){
			$items = $_head->getData('items');
			$tmpItems = array();
			$search = $type."/".$source;
			if(is_array($items)){
				$key_array = array_keys($items);
				foreach ($key_array as &$_key) {
					if ($search == $_key) {
						$tmpItems[$_key] = $items[$_key];
						unset($items[$_key]);
					}
				}
			}
			if ($type=='skin_css' && empty($params)) {
				$params = 'media="all"';
			}
			if (empty($tmpItems)) {
				$tmpItems[$type.'/'.$source] = array(
					'type'   => $type,
					'name'   => $source,
					'params' => $params,
					'if'     => $if,
					'cond'   => $cond,
					);
			}
			if($before){
				$items = array_merge($tmpItems, $items);
			}
			else{
				$items = array_merge($items, $tmpItems);
			}
			$_head->setData('items', $items);
		}

		return $this;
	}
}
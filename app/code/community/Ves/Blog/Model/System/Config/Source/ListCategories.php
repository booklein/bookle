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
class Ves_Blog_Model_System_Config_Source_ListCategories {

	public function toOptionArray() {

		$parent = 0;
		$collection = Mage::getModel('ves_blog/category')
		->getCollection()
		->addChildrentFilter( $parent );
		$output = array();
		$output[] = array(
			'value'  => 0,
			'label'  => "Select A Root"
			);
		foreach( $collection as $category ){
			$output[] = array(
				'value'  => $category->getId(),
				'label'  => $category->getTitle()
				);

			$sub = Mage::getModel('ves_blog/category')
			->getCollection()
			->addChildrentFilter( $category->getId() );

			if( count($sub) ){
				foreach( $sub as $a ){
					$output[] = array(
						'value'  => $a->getId(),
						'label'  => "--".$a->getTitle()
						);

					$sub1 = Mage::getModel('ves_blog/category')
					->getCollection()
					->addChildrentFilter( $a->getId() );

					if( count($sub1) ){
						foreach( $sub1 as $aa ){
							$output[] = array(
								'value'  => $aa->getId(),
								'label'  => "---".$aa->getTitle()
								);
						}
					}
				}
			}
		}

		return $output;
	}

}
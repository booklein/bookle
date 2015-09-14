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
class Ves_Blog_Model_Config_Source_Template
{
  const _DEFAULT       = 'default';
    //const CONTENT_BOTTOM    = 'CONTENT_BOTTOM';
	 /**
     * Design packages list getter
     * @return array
     */
   public function getPackageList()
   {
    $directory = Mage::getBaseDir('design') . DS . 'frontend';
    return $this->_listDirectories($directory);
  }

    /**
     * Design package (optional) themes list getter
     * @param string $package
     * @return string
     */
    public function getThemeList($package = null)
    {
      $result = array();

      if (is_null($package)){
        foreach ($this->getPackageList() as $package){
          $result[$package] = $this->getThemeList($package);
        }
      } else {
        $directory = Mage::getBaseDir('design') . DS . 'frontend' . DS . $package;
        $result = $this->_listDirectories($directory);
      }

      return $result;
    }

    private function _listDirectories($path, $fullPath = false)
    {
      $result = array();
      $dir = opendir($path);
      if ($dir) {
        while ($entry = readdir($dir)) {
          if (substr($entry, 0, 1) == '.' || !is_dir($path . DS . $entry)){
            continue;
          }
          if ($fullPath) {
            $entry = $path . DS . $entry;
          }
          $result[] = $entry;
        }
        unset($entry);
        closedir($dir);
      }

      return $result;
    }
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
		//$directory = Mage::getBaseDir('design') . DS . 'frontend' . DS . $package;
      $directory = Mage::getBaseDir('design') . DS . 'frontend' . DS . 'default' . DS . 'default' .  DS . 'template'.  DS . 'venus_blog'. DS . 'content';
      $directories = $this->_listDirectories($directory);
      $templates =  array(
       array('value' => self::_DEFAULT, 'label'=>Mage::helper('adminhtml')->__('default'))
       );
      foreach($directories as $key => $template){
       $templates[] = array('value' => $template, 'label'=>$template);
     }

     return $templates;
   }
 }

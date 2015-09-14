<?php
/*------------------------------------------------------------------------
 # VenusTheme Pagebuilder Module 
 # ------------------------------------------------------------------------
 # author:    VenusTheme.Com
 # copyright: Copyright (C) 2012 http://www.venustheme.com. All Rights Reserved.
 # @license: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Websites: http://www.venustheme.com
 # Technical Support:  http://www.venustheme.com/
-------------------------------------------------------------------------*/
//require_once(Mage::getBaseDir('code').'/community/Ves/Pagebuider/Helper/widgetbase.php');
class Ves_Autosearch_Block_Item extends Mage_Catalog_Block_Product_Abstract 
{	
	var $_config = array();
	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{	
		/*End init meida files*/
		parent::__construct();		
	}

	/**
     * Rendering block content
     *
     * @return string
     */
	function _toHtml() 
	{
		$this->assign("show_image", $this->getConfig("show_image"));
		$this->assign("thumbHeight", $this->getConfig("thumbHeight"));
		$this->assign("thumbWidth", $this->getConfig("thumbWidth"));
		$this->assign("show_price", $this->getConfig("show_price"));

		$this->setTemplate( "ves/autosearch/result_item.phtml" );
		
        return parent::_toHtml();
	}

	/**
	 * get value of the extension's configuration
	 *
	 * @return string
	 */
	function getConfig( $key, $panel='ves_autosearch' ){
		if(isset($this->_config[$key])) {
			return $this->_config[$key];
		} else {
			return Mage::getStoreConfig("ves_autosearch/$panel/$key");
		}
	}

}
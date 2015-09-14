<?php
class Ves_Base_Block_Widget_Googlemap extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface{

	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{
		parent::__construct( $attributes );
		$this->setTemplate( "ves/base/map.phtml" );

	}
	protected function _toHtml(){
		if(!Mage::getStoreConfig('ves_base/general_setting/show')) {
			return ;
		}
		$this->assign('description', $this->getConfig('description'));
        $this->assign('latitude', $this->getConfig('latitude'));
		$this->assign('longitude', $this->getConfig('longitude'));
		$this->assign('zoom', $this->getConfig('zoom'));
		$this->assign('width', $this->getConfig('width'));
		$this->assign('height', $this->getConfig('height'));
		$this->assign('is_preview', 1);
		return parent::_toHtml();
	}
	/**
	 * get value of the extension's configuration
	 *
	 * @return string
	 */
	public function getConfig( $key, $default = ""){
	    $value = $this->getData($key);
	    //Check if has widget config data
	    if($this->hasData($key) && $value !== null) {

	      if($value == "true") {
	        return 1;
	      } elseif($value == "false") {
	        return 0;
	      }
	      
	      return $value;
	      
	    }
	    return $default;
	}
}
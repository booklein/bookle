<?php
class Ves_TreeMenu_Block_Widget_Menu extends Ves_TreeMenu_Block_List implements Mage_Widget_Block_Interface
{
	/**
	 * Contructor
	 */
	public function __construct($attributes = array())
	{
	 	parent::__construct($attributes);
		
	}

	public function _toHtml() {
		$this->_show = $this->getConfig("show");
		
		if(!$this->_show) return;
		if(isset($this->_config) && $this->_config) {
			foreach($this->_config as $key=>$val) {
				if($this->hasData($key)) {
					$this->setConfig($key, $this->getData($key));
				}
			}
		}

        
        /* Assign html */
        $theme = ($this->getConfig('theme') != "") ? $this->getConfig('theme') : "default";
        //$this->_config['template'] = 'ves/treemenu/' . $theme . '/default.phtml';
        // render html

        if($this->_config['menumode'] == 'all'){
            $this->_config['template'] = 'ves/treemenu/' . $theme . '/default.phtml';
        }

        if($this->_config['menumode'] == 'auto'){
            $this->_config['template'] = 'ves/treemenu/' . $theme . '/list.phtml';
        }

        if($this->hasData("template")) {
            $this->setTemplate($this->getData("template"));
        } else {
            $this->setTemplate($this->_config['template']);
        }
        

        return parent::_toHtml();
	}
}
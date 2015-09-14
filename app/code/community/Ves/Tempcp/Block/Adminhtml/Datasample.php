<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Lof
 * @package     Lof_Coinslider
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Banner base block
 *
 * @category    Lof
 * @package     Lof_Coinslider
 * @author    
 */
class Ves_Tempcp_Block_Adminhtml_Datasample extends Mage_Adminhtml_Block_Widget_Form_Container {
    public function __construct() {

        parent::__construct();

        $this->_objectId = 'theme_id';
        $this->_blockGroup = 'ves_tempcp';
        $this->_controller = 'adminhtml_theme';

        $this->_headerText = Mage::helper('ves_tempcp')->__('Data Sample Manager');

        $this->removeButton('reset');
        $this->removeButton('delete');
        $this->removeButton('save');


        $themeHelper = Mage::helper('ves_tempcp/theme');

        $theme_data = $themeHelper->initTheme();

        $this->setThemeData($theme_data);

        $form_url = $this->getUrl('*/*/massInstall/', array("default_theme"=>1));

        $this->setFormActionUrl($form_url);

        $this->setTemplate('ves_tempcp/datasample.phtml');
    }

    protected function _prepareLayout() {

        $this->setChild('backbutton',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label'     => Mage::helper('ves_tempcp')->__('Back'),
                'onclick'   => 'setLocation(\'' . $this->getBackUrl() .'\')',
                'class'   => 'back'
                ))
        );
        
        $this->setChild('store_switcher', $this->getLayout()->createBlock('adminhtml/store_switcher')
                        ->setSwitchUrl($this->getUrl('*/*/*'))
                        ->setTemplate('ves_tempcp/store/switcher.phtml')
        );
        
        return parent::_prepareLayout();
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index/');
    }
    public function getStoreSwitcherHtml() {
        return $this->getChildHtml('store_switcher');
    }
    public function getBackButtonHtml() {
        return $this->getChildHtml('backbutton');
    }
    public function getDataSampleLink($theme_group = "") {
        $groups = explode("/", $theme_group);
        $package = (count($groups) > 1)?$groups[0]:'default';
        $design = isset($groups[1])?$groups[1]:$groups[0];

        return $this->getUrl('*/adminhtml_theme/massInstall', array('package'=>$package, 'design'=>$design));
    }

}
<?php
/**
 * Magento Ves Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Ves Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/ves-edition
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
 * @category    Ves
 * @package     Ves_Optimize
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/ves-edition
 */

/**
 * Viewed products container
 */
class Ves_Optimize_Model_Container_Viewedproducts extends Ves_Optimize_Model_Container_Abstract
{
    const COOKIE_NAME = 'VIEWED_PRODUCT_IDS';

    /**
     * Get viewed product ids from cookie
     *
     * @return array
     */
    protected function _getProductIds()
    {
        $result = $this->_getCookieValue(self::COOKIE_NAME, array());
        if ($result) {
            $result = explode(',', $result);
        }
        return $result;
    }

    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        $cacheId = $this->_placeholder->getAttribute('cache_id');
        $productIds = $this->_getProductIds();
        if ($cacheId && $productIds) {
            $cacheId = 'CONTAINER_' . md5($cacheId . implode('_', $productIds)
                . $this->_getCookieValue(Mage_Core_Model_Store::COOKIE_CURRENCY, ''));
            return $cacheId;
        }
        return false;
    }

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        /** @var $block Mage_Reports_Block_Product_Abstract */
        $block = $this->_getPlaceHolderBlock();
        $block->setProductIds($this->_getProductIds());
        $block->useProductIdsOrder();
        Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));
        return $block->toHtml();
    }

    /**
     * Save information about last viewed products
     *
     * @param array $productIds
     * @return Ves_Optimize_Model_Container_Viewedproducts
     * @deprecated after 1.8
     */
    protected function _registerProductsView($productIds)
    {
        return $this;
    }
}

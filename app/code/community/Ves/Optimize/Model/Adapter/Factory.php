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
 * Client adapter factory
 *
 * @category   Ves
 * @package    Ves_Optimize
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Ves_Optimize_Model_Adapter_Factory
{
    /**
     * Retrieves http curl adapter instance
     *
     * @return Varien_Http_Adapter_Curl
     */
    public function getHttpCurlAdapter()
    {
        return new Varien_Http_Adapter_Curl();
    }
}

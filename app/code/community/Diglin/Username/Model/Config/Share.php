<?php
/**
 * Diglin GmbH
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php

 *
 * @category    Diglin
 * @package     Diglin_Username
 * @copyright   Copyright (c) 2008-2015 Diglin GmbH - Switzerland (http://www.diglin.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer sharing config model
 *
 * @category   Diglin
 * @package    Diglin_Username
 */
class Diglin_Username_Model_Config_Share extends Mage_Customer_Model_Config_Share
{
    /**
     * Check for username duplicates before saving customers sharing options
     *
     * @return Mage_Customer_Model_Config_Share
     * @throws Mage_Core_Exception
     */
    public function _beforeSave()
    {
        parent::_beforeSave();

        $value = $this->getValue();
        if ($value == self::SHARE_GLOBAL) {
            if (Mage::getResourceSingleton('customer/customer')->findUsernameDuplicates()) {
                Mage::throwException(
                    Mage::helper('username')->__('Cannot share customer accounts globally because some customer accounts with the same username exist on multiple websites and cannot be merged.')
                );
            }
        }
        return $this;
    }
}

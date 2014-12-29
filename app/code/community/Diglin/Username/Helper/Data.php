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

class Diglin_Username_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isPersistentMustBeEnabled ()
    {
        return Mage::getStoreConfigFlag('username/general/enabled')
            && Mage::helper('core')->isModuleEnabled('persistent')
            && Mage::helper('core')->isModuleOutputEnabled('persistent')
            && Mage::helper('persistent')->isEnabled();
    }
}
<?php
/**
 * Diglin
 *
 * @category    Diglin
 * @package     Diglin_Username
 * @copyright   Copyright (c) 2011-2013 Diglin (http://www.diglin.com)
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
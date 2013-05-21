<?php
/**
 * Diglin
 *
 * @category    Diglin
 * @package     Diglin_Username
 * @copyright   Copyright (c) 2011-2013 Diglin (http://www.diglin.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Diglin_Username_Model_Config_Source_InputValidation
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'default', 'label'=>Mage::helper('username')->__('Default (letters, digits and _- characters)')),
            array('value'=>'alphanumeric', 'label'=>Mage::helper('username')->__('Letters and digits')),
            array('value'=>'alpha', 'label'=>Mage::helper('username')->__('Letters only')),
            array('value'=>'numeric', 'label'=>Mage::helper('username')->__('Digits only')),
        );
    }
}
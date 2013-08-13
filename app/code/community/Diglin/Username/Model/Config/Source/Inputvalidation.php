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
        $helper = Mage::helper('username');
        
        return array(
            array('value'=>'default', 'label'=> $helper->__('Default (letters, digits and _- characters)')),
            array('value'=>'alphanumeric', 'label'=> $helper->__('Letters and digits')),
            array('value'=>'alpha', 'label'=> $helper->__('Letters only')),
            array('value'=>'numeric', 'label'=> $helper->__('Digits only')),
        );
    }
}

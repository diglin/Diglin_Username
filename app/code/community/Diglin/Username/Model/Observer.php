<?php
/**
 * Diglin
 *
 * @category    Diglin
 * @package     Diglin_Username
 * @copyright   Copyright (c) 2011-2012 Diglin (http://www.diglin.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Diglin_Username_Model_Observer extends Mage_Customer_Model_Observer {
    
    public function isActive($observer)
    {   
        $customer = $observer->getEvent()->getModel();
        // Add the inactive option
        if($customer->getIsActive () != '1' ){
            throw new Mage_Core_Exception(Mage::helper('customer')->__('This account is disabled.'), 0);
        }
    }
    
    public function addAttributeToCollection ($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $collection->addAttributeToSelect('username');
    }
    
    /**
     * Change the attribute of username after the configuration
     * has been changed
     * 
     * @param Varien_Event_Observer $observer
     */
    public function changeEavAttribute ($observer)
    {
        $minLength = Mage::getStoreConfig('username/general/min_length');
        $maxLength = Mage::getStoreConfig('username/general/max_length');
        $inputValidation = Mage::getStoreConfig('username/general/input_validation');

        if($minLength > $maxLength) {
            Mage::throwException(
                Mage::helper('username')->__('Sorry but you cannot set a minimum length value %s bigger than the maximum length value %s. Please, change the values.',
                $minLength,
                $maxLength)
            );
        }

        /* @var $attributeUsernameModel Mage_Customer_Model_Attribute */
        $attributeUsernameModel = Mage::getModel('customer/attribute')->loadByCode('customer', 'username');
        $rules = $attributeUsernameModel->getValidateRules();
        $rules['max_text_length'] = $maxLength;
        $rules['min_text_length'] = $minLength;
        
        if($inputValidation != 'default') {
            $rules['input_validation'] = $inputValidation;
        }else {
            $rules['input_validation'] = '';
        }
        
        $attributeUsernameModel->setValidateRules($rules);
        $attributeUsernameModel->save();   
    }
}
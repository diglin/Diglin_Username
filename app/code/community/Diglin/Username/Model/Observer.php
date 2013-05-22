<?php
/**
 * Diglin
 *
 * @category    Diglin
 * @package     Diglin_Username
 * @copyright   Copyright (c) 2011-2013 Diglin (http://www.diglin.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Diglin_Username_Model_Observer extends Mage_Customer_Model_Observer
{
    /**
     * Test if the customer account is enabled or not
     *
     * Event: customer_customer_authenticated
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     */
    public function isActive($observer)
    {   
        $customer = $observer->getEvent()->getModel();
        // Add the inactive option
        if($customer->getIsActive () != '1' ){
            throw new Mage_Core_Exception(Mage::helper('customer')->__('This account is disabled.'), 0);
        }
    }

    /**
     * Add on the fly the username attribute to the customer collection
     *
     * Event: eav_collection_abstract_load_before
     *
     * @param Varien_Event_Observer $observer
     */
    public function addAttributeToCollection ($observer)
    {
        /* @var $collection Mage_Eav_Model_Entity_Collection_Abstract */
        $collection = $observer->getEvent()->getCollection();
        if ($collection->getEntity()->getType() == 'customer') {
            $collection->addAttributeToSelect('username');
        }

    }
    
    /**
     * Change the attribute of username after the configuration
     * has been changed
     *
     * Event: admin_system_config_changed_section_username
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
        if($attributeUsernameModel->getId()) {
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
<<<<<<< HEAD
=======
        
        $attributeUsernameModel->setValidateRules($rules);
        $attributeUsernameModel->save();
>>>>>>> 110cb6a43b166e9d2882c96ffe68c0b82da10964
    }
}
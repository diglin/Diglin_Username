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
class Diglin_Username_Model_Observer extends Mage_Customer_Model_Observer
{
    /**
     * Test if the customer account is enabled or not
     *
     * Event: customer_customer_authenticated
     *
     * @param Varien_Event_Observer $observer Observer
     * @throws Mage_Core_Exception
     */
    public function isActive($observer)
    {
        $customer = $observer->getEvent()->getModel();
        // Add the inactive option
        if ($customer->getIsActive() != '1') {
            throw new Mage_Core_Exception(Mage::helper('customer')->__('This account is disabled.'), 0);
        }
    }

    /**
     * Add on the fly the username attribute to the customer collection
     *
     * Event: eav_collection_abstract_load_before
     *
     * @param Varien_Event_Observer $observer Observer
     */
    public function addAttributeToCollection($observer)
    {
        /* @var $collection Mage_Eav_Model_Entity_Collection_Abstract */
        $collection = $observer->getEvent()->getCollection();
        $entity = $collection->getEntity();
        if (!empty($entity) && $entity->getType() == 'customer') {
            $collection->addAttributeToSelect('username');
        }

    }

    /**
     * Change the attribute of username after the configuration
     * has been changed
     *
     * Event: admin_system_config_changed_section_username
     *
     * @param Varien_Event_Observer $observer Observer
     */
    public function changeEavAttribute(Varien_Event_Observer $observer)
    {
        $minLength = Mage::getStoreConfig('username/general/min_length');
        $maxLength = Mage::getStoreConfig('username/general/max_length');
        $inputValidation = Mage::getStoreConfig('username/general/input_validation');

        if ($minLength > $maxLength) {
            Mage::throwException(
                Mage::helper('username')->
                __('Sorry but you cannot set a minimum length value %s bigger than the maximum length value %s. Please, change the values.',
                    $minLength,
                    $maxLength)
            );
        }

        /* @var $attributeUsernameModel Mage_Customer_Model_Attribute */
        $attributeUsernameModel = Mage::getModel('customer/attribute')->loadByCode('customer', 'username');
        if ($attributeUsernameModel->getId()) {
            $rules = $attributeUsernameModel->getValidateRules();
            $rules['max_text_length'] = $maxLength;
            $rules['min_text_length'] = $minLength;

            if ($inputValidation != 'default' && $inputValidation != 'custom') {
                $rules['input_validation'] = $inputValidation;
            } else {
                $rules['input_validation'] = '';
            }

            $attributeUsernameModel->setValidateRules($rules);
            $attributeUsernameModel->save();
        }
    }

    /**
     * Event
     * - core_block_abstract_to_html_before
     *
     * @param Varien_Event_Observer $observer Observer
     */
    public function addUsernameColumn(Varien_Event_Observer $observer)
    {
        if (!Mage::getStoreConfigFlag('username/general/grid')) {
            return;
        }

        $grid = $observer->getBlock();

        /**
         * Mage_Adminhtml_Block_Customer_Grid
         */
        if ($grid instanceof Mage_Adminhtml_Block_Customer_Grid) {
            $grid->addColumnAfter(
                'username',
                array(
                    'header' => Mage::helper('username')->__('Username'),
                    'index' => 'username'
                ),
                'email'
            );
        }
    }
}
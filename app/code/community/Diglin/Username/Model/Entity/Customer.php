<?php
/**
 * Diglin
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Diglin
 * @package     Diglin_Username
 * @copyright   Copyright (c) 2011-2014 Diglin (http://www.diglin.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Diglin_Username_Model_Entity_Customer extends Mage_Customer_Model_Resource_Customer {
    
    protected function _beforeSave(Varien_Object $customer)
    {
        parent::_beforeSave($customer);

        if (Mage::getStoreConfigFlag('username/general/enabled')) {
            if ($customer->getSharingConfig()->isWebsiteScope()) {
                $websiteId = (int) $customer->getWebsiteId();
            }else{
                $websiteId = null;
            }

            $model = Mage::getModel('customer/customer');
            $result = $model->customerUsernameExists($customer->getUsername(), $websiteId);
            if ($result && $result->getId() != $customer->getId()) {
                throw Mage::exception('Mage_Core', Mage::helper('username')->__("Username already exists"));
            }
        }

        return $this;
    }
    
    protected function _getDefaultAttributes()
    {
        $attributes = parent::_getDefaultAttributes();
        array_push($attributes, 'is_active');
        return $attributes;
    }
    
    /**
     * Load customer by username
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param string $username
     * @return Mage_Customer_Model_Entity_Customer
     * @throws Mage_Core_Exception
     */
    public function loadByUsername(Mage_Customer_Model_Customer $customer, $username)
    {
        if (!Mage::getStoreConfigFlag('username/general/case_sensitive')) {
            $filter = new Zend_Filter_StringToLower(array('encoding' => 'UTF-8'));
            $username = $filter->filter($username);
        }
        $select = $this->_getReadAdapter()->select()
            ->from($this->getEntityTable(), array($this->getEntityIdField()))
            ->joinNatural(array('cev' => $this->getTable('customer_entity_varchar')))
            ->joinNatural(array('ea' => $this->getTable('eav/attribute')))
            ->where('ea.attribute_code=\'username\' AND cev.value=?',$username);

        if ($customer->getSharingConfig()->isWebsiteScope()) {
            if (!$customer->hasData('website_id')) {
                Mage::throwException(Mage::helper('customer')->__('Customer website ID must be specified when using the website scope.'));
            }
            $select->where('website_id=?', (int)$customer->getWebsiteId());
        }

        if ($id = $this->_getReadAdapter()->fetchOne($select, 'entity_id')) {
            $this->load($customer, $id);
        }
        else {
            $customer->setData(array());
        }
        return $this;
    }

    /**
     * Check whether there are username duplicates of customers in global scope
     *
     * @return bool
     */
    public function findUsernameDuplicates()
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from(array('cev' => $this->getTable('customer_entity_varchar')), array('cnt' => 'COUNT(*)'))
            ->joinLeft(array('ea' => $this->getTable('eav/attribute')), 'ea.attribute_id = cev.attribute_id')
            ->where('ea.attribute_code=\'username\'')
            ->group('cev.value')
            ->order('cnt DESC')
            ->limit(1);

        $lookup = $adapter->fetchRow($select);
        if (empty($lookup)) {
            return false;
        }
        return $lookup['cnt'] > 1;
    }
}
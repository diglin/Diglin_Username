<?php
/**
 * Diglin
 *
 * @category    Diglin
 * @package     Diglin_Username
 * @copyright   Copyright (c) 2011-2012 Diglin (http://www.diglin.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Diglin_Username_Model_Entity_Customer extends Mage_Customer_Model_Entity_Customer{
    
    protected function _beforeSave(Varien_Object $customer)
    {
        parent::_beforeSave($customer);

        if ($customer->getSharingConfig()->isWebsiteScope()) {
            $websiteId = (int) $customer->getWebsiteId();
        }else{
            $websiteId = null;
        }
        
        $model = Mage::getModel('customer/customer');
        $result = $model->customerUsernameExists($customer->getUsername(), $websiteId);
        if ($result && $result->getId() != $customer->getId()) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__("Username already exists"));
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
        $filter = new Zend_Filter_StringToLower(array('encoding' => 'UTF-8'));
        $username = $filter->filter($username);
        $select = $this->_getReadAdapter()->select()
            ->from($this->getEntityTable(), array($this->getEntityIdField()))
            ->joinNatural(array('cev' => $this->getTable('customer_entity_varchar')))
            ->joinNatural(array('ea' => $this->getTable('eav_attribute')))
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
}
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

class Diglin_Username_Model_Import_Entity_Customer extends Mage_ImportExport_Model_Import_Entity_Customer
{
    /**
     * Gather and save information about customer entities.
     *
     * Diglin: make is_active for new customer importeable
     * clst: make is_active updateable for existing customers
     *
     * @return Mage_ImportExport_Model_Import_Entity_Customer
     */
    protected function _saveCustomers()
    {
        /** @var $resource Mage_Customer_Model_Customer */
        $resource       = Mage::getModel('customer/customer');
        $strftimeFormat = Varien_Date::convertZendToStrftime(Varien_Date::DATETIME_INTERNAL_FORMAT, true, true);
        $table = $resource->getResource()->getEntityTable();
        $nextEntityId   = Mage::getResourceHelper('importexport')->getNextAutoincrement($table);
        $passId         = $resource->getAttribute('password_hash')->getId();
        $passTable      = $resource->getAttribute('password_hash')->getBackend()->getTable();

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityRowsIn = array();
            $entityRowsUp = array();
            $attributes   = array();

            $oldCustomersToLower = array_change_key_case($this->_oldCustomers, CASE_LOWER);

            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    continue;
                }
                if (self::SCOPE_DEFAULT == $this->getRowScope($rowData)) {
                    // entity table data
                    $entityRow = array(
                        'group_id'   => empty($rowData['group_id']) ? self::DEFAULT_GROUP_ID : $rowData['group_id'],
                        'store_id'   => empty($rowData[self::COL_STORE])
                                        ? 0 : $this->_storeCodeToId[$rowData[self::COL_STORE]],
                        'created_at' => empty($rowData['created_at'])
                                        ? now() : gmstrftime($strftimeFormat, strtotime($rowData['created_at'])),
                        'updated_at' => now(),
                        'is_active'  => empty($rowData['is_active']) ? 1 : $this->_attributes['is_active']['options'][$rowData['is_active']]
                    );
                    $emailToLower = strtolower($rowData[self::COL_EMAIL]);
                    if (isset($oldCustomersToLower[$emailToLower][$rowData[self::COL_WEBSITE]])) { // edit
                        $entityId = $oldCustomersToLower[$emailToLower][$rowData[self::COL_WEBSITE]];
                        $entityRow['entity_id'] = $entityId;
                        $entityRowsUp[] = $entityRow;
                    } else { // create
                        $entityId                      = $nextEntityId++;
                        $entityRow['entity_id']        = $entityId;
                        $entityRow['entity_type_id']   = $this->_entityTypeId;
                        $entityRow['attribute_set_id'] = 0;
                        $entityRow['website_id']       = $this->_websiteCodeToId[$rowData[self::COL_WEBSITE]];
                        $entityRow['email']            = $rowData[self::COL_EMAIL];
                        $entityRow['is_active']        = 1;
                        $entityRowsIn[]                = $entityRow;

                        $this->_newCustomers[$rowData[self::COL_EMAIL]][$rowData[self::COL_WEBSITE]] = $entityId;
                    }
                    // attribute values
                    foreach (array_intersect_key($rowData, $this->_attributes) as $attrCode => $value) {
                        if (!$this->_attributes[$attrCode]['is_static'] && strlen($value)) {
                            /** @var $attribute Mage_Customer_Model_Attribute */
                            $attribute  = $resource->getAttribute($attrCode);
                            $backModel  = $attribute->getBackendModel();
                            $attrParams = $this->_attributes[$attrCode];

                            if ('select' == $attrParams['type']) {
                                $value = $attrParams['options'][strtolower($value)];
                            } elseif ('datetime' == $attrParams['type']) {
                                $value = gmstrftime($strftimeFormat, strtotime($value));
                            } elseif ($backModel) {
                                $attribute->getBackend()->beforeSave($resource->setData($attrCode, $value));
                                $value = $resource->getData($attrCode);
                            }
                            $attributes[$attribute->getBackend()->getTable()][$entityId][$attrParams['id']] = $value;

                            // restore 'backend_model' to avoid default setting
                            $attribute->setBackendModel($backModel);
                        }
                    }
                    // password change/set
                    if (isset($rowData['password']) && strlen($rowData['password'])) {
                        $attributes[$passTable][$entityId][$passId] = $resource->hashPassword($rowData['password']);
                    }
                }
            }
            $this->_saveCustomerEntity($entityRowsIn, $entityRowsUp)->_saveCustomerAttributes($attributes);
        }
        return $this;
    }
    
    /**
     * Update and insert data in entity table.
     * 
     * clst: make is_active updateable for existing customers
     *
     * @param array $entityRowsIn Row for insert
     * @param array $entityRowsUp Row for update
     * @return Mage_ImportExport_Model_Import_Entity_Customer
     */
    protected function _saveCustomerEntity(array $entityRowsIn, array $entityRowsUp)
    {
        if ($entityRowsIn) {
            $this->_connection->insertMultiple($this->_entityTable, $entityRowsIn);
        }
        if ($entityRowsUp) {
            $this->_connection->insertOnDuplicate(
                $this->_entityTable,
                $entityRowsUp,
                array('group_id', 'store_id', 'updated_at', 'created_at', 'is_active')
            );
        }
        return $this;
    }

}
<?php
/**
 * Diglin
 *
 * @category    Diglin
 * @package     Diglin_Username
 * @copyright   Copyright (c) 2011-2013 Diglin (http://www.diglin.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Diglin_Username_Model_Entity_Setup */
$installer = $this;

/* @var $eavConfig Mage_Eav_Model_Config */
$usernameAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'username');

$installer->startSetup();

$select = $installer->getConnection()->select()
    ->from($this->getTable('customer_entity_varchar'), 'entity_id')
    ->where('attribute_id = ?', $usernameAttribute->getId());

$ids = $installer->getConnection()->fetchCol($select);

$select = $installer->getConnection()->select()
    ->from(array('c' => $this->getTable('customer_entity')), 'email');

if (!empty($ids)) {
	$select->joinLeft(array('cev' => $this->getTable('customer_entity_varchar')), 'c.entity_id = cev.entity_id')
	->where('cev.entity_id NOT IN ('. implode(',', $ids) . ')');
}

// Create username for old customers to prevent problem when creating an order as a guest
$customers = $installer->getConnection()->fetchAll($select);
foreach ($customers as $customer){
    $customer['attribute_id'] = $usernameAttribute->getId();
    $email = $customer['email'];
    $pos = strpos($email, '@');
    $customer['value'] = substr($email, 0, $pos) . substr(uniqid(), 0, 5);
    unset($customer['email']);
    unset($customer['value_id']);

    $installer->getConnection()->insert($this->getTable('customer_entity_varchar'), $customer);
}

$installer->endSetup();
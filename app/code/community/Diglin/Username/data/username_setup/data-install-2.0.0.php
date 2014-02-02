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
    ->from(array('c' => $this->getTable('customer_entity')), array('email', 'entity_id', 'entity_type_id'));

if (!empty($ids)) {
	$select->joinLeft(array('cev' => $this->getTable('customer_entity_varchar')), 'c.entity_id = cev.entity_id')
	    ->where('cev.entity_id NOT IN ('. implode(',', $ids) . ')');
}

// @todo - add support for Customer Website Share option (check that the username doesn't already exist in other websites)

// Create username for old customers to prevent problem when creating an order as a guest
$customers = $installer->getConnection()->fetchAll($select);
foreach ($customers as $customer) {
    $customer['attribute_id'] = $usernameAttribute->getId();
    $email = $customer['email'];
    $pos = strpos($email, '@');
    $customer['value'] = substr($email, 0, $pos) . substr(uniqid(), 0, 5) . $customer['entity_id'];
    
    unset($customer['email']);
    unset($customer['value_id']);

    $installer->getConnection()->insert($this->getTable('customer_entity_varchar'), $customer);
}

$installer->endSetup();
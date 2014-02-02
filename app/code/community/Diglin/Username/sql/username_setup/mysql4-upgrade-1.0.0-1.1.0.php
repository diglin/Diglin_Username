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
$eavConfig = Mage::getSingleton('eav/config');
$usernameAttribute = $eavConfig->getAttribute('customer', 'username');

$installer->startSetup();

$result = $installer->getConnection()->raw_fetchRow("SHOW COLUMNS from {$this->getTable('sales_flat_order')} like '%customer_username%'");
if(!is_array($result) || !in_array('customer_username', $result)){
$installer->run("
    ALTER TABLE  `{$this->getTable('sales_flat_order')}`
        ADD  `customer_username` VARCHAR( 255 ) NULL AFTER  `customer_taxvat`
    ");
    // can be a fix for bug of this module in Magento > 1.5
}


$select = new Zend_Db_Select($installer->getConnection());
$select->from(array('c' => $this->getTable('customer_entity')), 'email')
    ->joinLeft(array('cev' => $this->getTable('customer_entity_varchar')), 'c.entity_id = cev.entity_id')
    ->where("cev.entity_id NOT IN (SELECT entity_id FROM `{$this->getTable('customer_entity_varchar')}` WHERE attribute_id = {$usernameAttribute->getId()})")
    ->group('c.entity_id');

// Create username for old customers to prevent problem when creating an order
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
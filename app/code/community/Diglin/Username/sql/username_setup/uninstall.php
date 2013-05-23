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

$installer->deleteTableRow($installer->getTable('eav_attribute'), 'attribute_code', 'username');
$installer->getConnection()->dropColumn($installer->getTable('sales/quote'), 'customer_username');
$installer->getConnection()->dropColumn($installer->getTable('sales/order'), 'customer_username');
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

$store = Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$attributes = $installer->getAdditionalAttributes();

foreach ($attributes as $attributeCode => $data) {
    $installer->addAttribute('customer', $attributeCode, $data);

    $attribute = $eavConfig->getAttribute('customer', $attributeCode);
    $attribute->setWebsite( (($store->getWebsite()) ? $store->getWebsite() : 0));

    if (false === ($attribute->getIsSystem() == 1 && $attribute->getIsVisible() == 0)) {
        $usedInForms = array(
            'customer_account_create',
            'customer_account_edit',
            'checkout_register',
        );
        if (!empty($data['adminhtml_only'])) {
            $usedInForms = array('adminhtml_customer');
        } else {
            $usedInForms[] = 'adminhtml_customer';
        }
        if (!empty($data['adminhtml_checkout'])) {
            $usedInForms[] = 'adminhtml_checkout';
        }

        $attribute->setData('used_in_forms', $usedInForms);
    }
    $attribute->save();
}

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/quote'), 'customer_username', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '255',
    'nullable' => true,
    'comment' => 'Customer Username'
));

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'customer_username', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '255',
    'nullable' => true,
    'comment' => 'Customer Username'
));

$installer->endSetup();
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

$installer = $this;

/* @var $installer Diglin_Username_Model_Entity_Setup */
$store = Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID);

/* @var $eavConfig Mage_Eav_Model_Config */
$eavConfig = Mage::getSingleton('eav/config');

$attribute = array(
    'label'        => 'Username',
    'visible'      => true,
    'required'     => true,
    'type'         => 'varchar',
    'input'        => 'text',
    'sort_order'    => 65,
    'validate_rules'    => array(
            'max_text_length'   => 30,
            'min_text_length'   => 1
    ),
    'used_in_forms' => array('adminhtml_customer','adminhtml_checkout','customer_account_edit', 'customer_account_create', 'checkout_register'),
);

$installer->addAttribute('customer','username', $attribute);

$attributes  = array('username' => $attribute);

foreach ($attributes as $attributeCode => $data) {
    $attribute = $eavConfig->getAttribute('customer', $attributeCode);
    $attribute->setWebsite( (($store->getWebsite())?$store->getWebsite():0));
    $attribute->addData($data);
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
        }else {
            $usedInForms[] = 'adminhtml_checkout';
        }

        $attribute->setData('used_in_forms', $usedInForms);
    }
    $attribute->save();
}

$installer->startSetup();

$result = $installer->getConnection()->raw_fetchRow("SHOW COLUMNS from {$this->getTable('sales_flat_quote')} like '%customer_username%'");
if(!is_array($result) || !in_array('customer_username', $result)){
$installer->run("
    ALTER TABLE  `{$this->getTable('sales_flat_quote')}`
        ADD  `customer_username` VARCHAR( 255 ) NULL AFTER  `customer_taxvat`
    ");
}

$installer->endSetup();
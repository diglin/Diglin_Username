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

$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId('customer');
$defaultAttributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);

$attribute = $installer->getAttribute($entityTypeId, 'is_active');
if($attribute) $installer->removeAttribute($entityTypeId, 'is_active');

$installer->addAttribute('customer', 'is_active',  array(
        'group'         => 'Account information',
        'label'         => 'Active',
        'type'          => 'static',
        'input'         => 'select',
        'user_defined'  => true,
        'source'        => 'eav/entity_attribute_source_boolean',
        'required'      => false,
        'default'       => true,
        'visible'       => true,
        'visible_on_front' => false,
    )
);
       
Mage::getSingleton('eav/config')
       ->getAttribute('customer', 'is_active')
       ->setData('used_in_forms', array('adminhtml_customer'))
       ->save();

$installer->endSetup();
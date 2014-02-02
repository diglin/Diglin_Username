<?php
/**
 *
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

$installer->updateAttribute('customer', 'username', 'is_required', 0);

$installer->startSetup();

$select = $installer->getConnection()->select()
    ->from($installer->getTable('core_config_data'), 'config_id')
    ->where ('path = ?', 'username/general/force_tolower');

$ids = $installer->getConnection()->fetchCol($select);

foreach ($ids as $id) {
    $installer->getConnection()->update($installer->getTable('core_config_data'), array('path' => 'username/general/case_sensitive'), array('config_id = ?' => $id));
}

$installer->endSetup();
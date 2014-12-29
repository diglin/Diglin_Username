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
$installer->updateAttribute('customer', 'username', 'validate_rules', serialize(array('max_text_length' => 30, 'min_text_length' => 6)));
//$installer->updateAttribute('customer', 'username', 'used_in_forms', serialize( array('customer_address_edit'))); // For onepage !

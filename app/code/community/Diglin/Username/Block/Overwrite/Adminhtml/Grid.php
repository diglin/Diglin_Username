<?php
/**
 * Diglin
 *
 * @category    Diglin
 * @package     Diglin_Username
 * @copyright   Copyright (c) 2011-2013 Diglin (http://www.diglin.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Diglin_Username_Block_Overwrite_Adminhtml_Grid extends Mage_Adminhtml_Block_Customer_Grid
{
    
    protected function _prepareColumns()
    {
        if (Mage::getStoreConfigFlag('username/general/grid')) {
            // Set a new column username after the column name
            $this->addColumnAfter('username', array(
                'header'    => Mage::helper('customer')->__('Username'),
                'index'     => 'username'
            ),
            'name');
        }
        return parent::_prepareColumns();
    }
}
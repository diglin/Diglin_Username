<?php
/**
 * Diglin
 *
 * @category    Diglin
 * @package     Diglin_Username
 * @copyright   Copyright (c) 2011-2012 Diglin (http://www.diglin.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Diglin_Username_Model_Entity_Setup extends Mage_Customer_Model_Entity_Setup{

    public function getDefaultEntities()
    {
        $entities = parent::getDefaultEntities();
        
        $entities['customer']['attributes'] = array(
            'username' => array(
                'type'    => 'varchar',
                'input'    => 'text',
                'label'         => 'Username',
                'sort_order'    => 44,  
            )
        );
        
        $entities['customer']['attributes'][] = array('is_active' => array(
                        'label'         => 'Active',
                        'visible'       => true,
                        'sort_order'    => 40,
        ));
        
        return $entities;
    }
}
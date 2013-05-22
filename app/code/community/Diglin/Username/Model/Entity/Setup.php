<?php
/**
 * Diglin
 *
 * @category    Diglin
 * @package     Diglin_Username
 * @copyright   Copyright (c) 2011-2013 Diglin (http://www.diglin.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Diglin_Username_Model_Entity_Setup extends Mage_Customer_Model_Resource_Setup
{

    public function getDefaultEntities()
    {
        return $this->getAdditionalAttributes(parent::getDefaultEntities());
    }

    /**
     *
     * To be used directly by install script or by setup class
     *
     * @param null $entities
     * @return array
     */
    public function getAdditionalAttributes ($entities = null)
    {
        $newEntity = array(
            'username' => array(
                'type'    => 'varchar',
                'input'    => 'text',
                'label'         => 'Username',
                'visible'      => true,
                'required'     => false,
                'sort_order'    => 44,
                'position'    => 44,
                'adminhtml_customer' => 1,
                'adminhtml_checkout' => 1,
                'validate_rules'    => serialize(array(
                    'max_text_length'   => 30,
                    'min_text_length'   => 6
                )),
            ),
            'is_active' => array(
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
                'sort_order'    => 40,
                'position'    => 40,
                'adminhtml_only' => 1
            )
        );

        // In this case we just need the array of the data and set manually the attribute
        if (is_null($entities)) {
            return $newEntity;
        }

        return $entities['customer']['attributes'] = array_merge($entities['customer']['attributes'], $newEntity);
    }
}
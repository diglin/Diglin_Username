<?php
/**
 * Diglin GmbH
 *
 * @category    Diglin
 * @package     Diglin_Username
 * @author      Sylvain Rayé <support@diglin.com>
 * @copyright   Copyright (c) 2008-2015 Diglin GmbH - Switzerland (http://www.diglin.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Diglin_Username_Adminhtml_SyncController
 */
class Diglin_Username_Adminhtml_SyncController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Return acl synchronize singleton
     *
     * @return Diglin_Username_Model_Generate_Flag
     */
    protected function getSyncFlag()
    {
        return Mage::getSingleton('username/generate_flag')->loadSelf();
    }

    /**
     * @todo to finish to implement by using flag and using Ajax response
     */
    public function generateAction()
    {
        session_write_close();

        $flag = $this->getSyncFlag();
        $flag
            ->setState(Diglin_Username_Model_Generate_Flag::STATE_RUNNING)
            ->save();

        $flag->setFlagData(array());

        try {
            /* @var $eavConfig Mage_Eav_Model_Config */
            $usernameAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'username');

            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');

            $select = $readConnection
                ->select()
                ->from($resource->getTableName('customer_entity_varchar'), 'entity_id')
                ->where('attribute_id = ?', $usernameAttribute->getId())
                ->where('value IS NOT NULL');

            $ids = $readConnection->fetchCol($select);

            $select = $readConnection
                ->select()
                ->from(array('c' => $resource->getTableName('customer_entity')), array('email', 'entity_id', 'entity_type_id'))
                ->group('c.entity_id');

            if (!empty($ids)) {
                $select
                    ->joinLeft(array('cev' => $resource->getTableName('customer_entity_varchar')), 'c.entity_id = cev.entity_id')
                    ->where('cev.entity_id NOT IN (' . implode(',', $ids) . ')');
            }

            // @todo - add support for Customer Website Share option (check that the username doesn't already exist in other websites)
            // @todo - add support for username depending on the username type supported in the configuration (only letters, digits, etc)

            // Create username for old customers to prevent problem when creating an order as a guest
            $customers = $readConnection->fetchAll($select);
            $totalItemsDone = 0;

            $flagData['total_items'] = count($customers);
            $flag->setFlagData($flagData)
                ->save();

            foreach ($customers as $customer) {
                $customer['attribute_id'] = $usernameAttribute->getId();
                $email = $customer['email'];
                $pos = strpos($email, '@');
                $customer['value'] = substr($email, 0, $pos) . substr(uniqid(), 0, 5) . $customer['entity_id'];

                unset($customer['email']);
                unset($customer['value_id']);

                $readConnection->query('REPLACE INTO '
                    . $readConnection->getTableName('customer_entity_varchar')
                    . ' SET entity_id = :entity_id, entity_type_id = :entity_type_id, attribute_id = :attribute_id, value = :value',
                    $customer);

                $flagData['total_items_done'] = $totalItemsDone;
                $flag->setFlagData($flagData)
                    ->save();
            }

        } catch (Exception $e) {
            Mage::logException($e);
            $flag->setHasErrors(true);
        }
        $flag->setState(Diglin_Username_Model_Generate_Flag::STATE_FINISHED)->save();
    }

    /**
     * Get status of the sync
     */
    public function syncstatusAction()
    {
        $flag = $this->getSyncFlag();
        if ($flag) {
            $state = $flag->getState();
            $flagData = $flag->getFlagData();

            switch ($state) {
                case Diglin_Username_Model_Generate_Flag::STATE_RUNNING:
                    if ($flagData['total_items'] > 0) {
                        $percent = (int)($flagData['total_items_done'] * 100 / $flagData['total_items']) . '%';
                        $result['message'] = Mage::helper('username')->__('Generating username: %s done.', $percent);
                    } else {
                        $result ['message'] = Mage::helper('username')->__('Generating...');
                    }
                    break;
                case Diglin_Username_Model_Generate_Flag::STATE_FINISHED:
                    Mage::dispatchEvent('add_username_generate_message');
                    $result ['message'] = Mage::helper('username')->__('Generation finished');

                    if ($flag->getHasErrors()) {
                        $result ['message'] .= Mage::helper('username')->__('Errors occurred while running. Please, check the log if enabled.');
                        $result ['has_errors'] = true;
                    }
                    $state = Diglin_Username_Model_Generate_Flag::STATE_NOTIFIED;
                    break;
                case Diglin_Username_Model_Generate_Flag::STATE_NOTIFIED:
                    break;
                default:
                    $state = Diglin_Username_Model_Generate_Flag::STATE_INACTIVE;
                    break;
            }
        } else {
            $state = Diglin_Username_Model_Generate_Flag::STATE_INACTIVE;
        }
        $result['state'] = $state;

        $result = Mage::helper('core')->jsonEncode($result);
        Mage::app()->getResponse()->setBody($result);
    }
}
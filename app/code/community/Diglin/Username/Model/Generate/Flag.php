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
 * Class Diglin_Username_Model_Generate_Flag
 */
class Diglin_Username_Model_Generate_Flag extends Mage_Core_Model_Flag
{
    /**
     * There was no generation
     */
    const STATE_INACTIVE    = 0;
    /**
     * Generation process is active
     */
    const STATE_RUNNING     = 1;
    /**
     * Generation is finished
     */
    const STATE_FINISHED    = 2;
    /**
     * Generation finished and notify message was formed
     */
    const STATE_NOTIFIED    = 3;

    /**
     * Flag time to life in seconds
     */
    const FLAG_TTL          = 300;

    /**
     * Generation flag code
     *
     * @var string
     */
    protected $_flagCode    = 'username_generate';

    /**
     * Pass error to flag
     *
     * @param Exception $e
     * @return Diglin_Username_Model_Generate_Flag
     */
    public function passError(Exception $e)
    {
        $data = $this->getFlagData();
        if (!is_array($data)) {
            $data = array();
        }
        $data['has_errors'] = true;
        $this->setFlagData($data);
        return $this;
    }
}

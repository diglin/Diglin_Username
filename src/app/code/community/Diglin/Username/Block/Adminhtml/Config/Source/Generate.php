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
 * Class Diglin_Username_Block_Adminhtml_Config_Source_Generate
 */
class Diglin_Username_Block_Adminhtml_Config_Source_Generate extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
    */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('username/system/config/generate.phtml');
    }

    /**
     * Remove scope label
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for synchronize button
     *
     * @return string
     */
    public function getAjaxSyncUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('username/sync/generate');
    }

    /**
     * Return ajax url for synchronize button
     *
     * @return string
     */
    public function getAjaxStatusUpdateUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('username/sync/syncstatus');
    }

    /**
     * Generate generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
        ->setData(array(
                'id'        => 'generate_button',
                'label'     => $this->helper('username')->__('Generate'),
                'onclick'   => 'javascript:generate(); return false;'
        ));

        return $button->toHtml();
    }
}

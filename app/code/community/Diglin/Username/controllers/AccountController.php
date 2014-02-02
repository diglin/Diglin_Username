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

require_once 'Mage/Customer/controllers/AccountController.php';

class Diglin_Username_AccountController extends Mage_Customer_AccountController
{
    /**
     * Rewrite to allow support of Username
     *
     */
    public function forgotPasswordPostAction()
    {
        $email = (string) $this->getRequest()->getPost('email');
        if ($email) {

            /** @var $customer Diglin_Username_Model_Customer */
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByUsername($email);

            if (!$customer->getId() && !Zend_Validate::is($email, 'EmailAddress')) {
                $this->_getSession()->setForgottenEmail($email);
                $this->_getSession()->addError($this->__('Invalid email address.'));
                $this->_redirect('*/*/forgotpassword');
                return;
            } else if (!$customer->getId()) {
                // Load by Email if username not found and email seems to be valid
                $customer
                    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                    ->loadByEmail($email);
            }

            if ($customer->getId()) {
                try {
                    $newResetPasswordLinkToken = $this->_getHelper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
                } catch (Exception $exception) {
                    $this->_getSession()->addError($exception->getMessage());
                    $this->_redirect('*/*/forgotpassword');
                    return;
                }
            }
            $this->_getSession()
                ->addSuccess( $this->_getHelper('customer')
                    ->__('If there is an account associated with %s you will receive an email with a link to reset your password.',
                        $this->_getHelper('customer')->escapeHtml($email)));
            $this->_redirect('*/*/');
            return;
        } else {
            $this->_getSession()->addError(Mage::helper('username')->__('Please enter your email or username.'));
            $this->_redirect('*/*/forgotpassword');
            return;
        }
    }
}

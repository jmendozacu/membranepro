<?php
require_once Mage::getModuleDir('controllers', 'Mage_Customer') . DS . 'AccountController.php';
//we need to add this one since Magento wont recognize it automatically

class Busteco_AjaxLogin_AccountController extends Mage_Customer_AccountController
{
    /**
     * Login post action
     */
    public function loginPostAction()
    {
        if ($this->getRequest()->getParam('context') == 'checkout') {
            parent::loginPostAction();
            return;
        }
        
        // Zend_Debug::dump($this->getRequest()->getParams()); die();
        
        if (!$this->_validateFormKey()) {
            return;
        }
        
        if ($this->_getSession()->isLoggedIn()) {
            return;
        }
        
        $session = $this->_getSession();
        $message = '';
        
        if ($this->getRequest()->isPost()) {
            
            if (!$this->getRequest()->getPost('is_ajax')) {
                parent::loginPostAction();
                return;
            }
            
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                    
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = $this->_getHelper('customer')->__('Contul nu este confirmat. <a href="%s">Clic aici</a> pentru a retrimite email-ul de confirmare.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                $message = $this->__('Email şi Parolă sunt câmpuri obligatorii.');
                $this->getResponse()->setBody($message);
            }
        }
        
        // /Zend_Debug::dump($message); die();
        $this->getResponse()->setBody($message);
    }

    /**
     * Forgot customer password action
     */
    public function forgotPasswordPostAction()
    {
        $email = (string) $this->getRequest()->getPost('email');
        if ($email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->_getSession()->setForgottenEmail($email);
                $message = $this->__('Adresa de email este invalidă.');
                echo $message;
                return;
            }

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {
                try {
                    $newResetPasswordLinkToken =  $this->_getHelper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
                } catch (Exception $exception) {
                    $message = $exception->getMessage();
                    echo $message;
                    return;
                }
            }
            $message = $this->__('Verificaţi-vă adresa de email pentru instrucţiunile de resetare a parolei.');
            echo $message;
            return;
        } else {
            
            $message = $this->__('Vă rugăm precizaţi adresa de email.');
            echo $message;
            return;
        }
    }

}

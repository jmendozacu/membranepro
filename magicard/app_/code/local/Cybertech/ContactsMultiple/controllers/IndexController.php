<?php

require_once 'Mage/Contacts/controllers/IndexController.php';

class Cybertech_ContactsMultiple_IndexController extends Mage_Contacts_IndexController
{

    const ADDRESS_INFO			= 'info@printcard.ro';
    const ADDRESS_ORDERS		= 'comenzi@printcard.ro';
    const ADDRESS_PRODUCTION	= 'productie@printcard.ro';
    const ADDRESS_SUPPORT		= 'suport@printcard.ro';

    public function preDispatch()
    {
        parent::preDispatch();
    }

    public function indexAction()
    {
        parent::indexAction();
    }

    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        if ( $post ) {
// Select e-mail addres according to the form field		
			switch($post['department']) {
					case 'info':
						$recipientAddress = self::ADDRESS_INFO;
						break;
					case 'orders':
						$recipientAddress = self::ADDRESS_ORDERS;
						break;
					case 'production':
						$recipientAddress = self::ADDRESS_PRODUCTION;
						break;
					case 'support':
						$recipientAddress = self::ADDRESS_SUPPORT;
						break;
					default:
						$recipientAddress = Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT);
						break;
				}
// End of e-mail selection
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try {
                $postObject = new Varien_Object();
                $postObject->setData($post);

                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }
                $mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        $recipientAddress,
                        null,
                        array('data' => $postObject)
                    );

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/');
                return;
            }

        } else {
            $this->_redirect('*/*/');
        }
    }

}

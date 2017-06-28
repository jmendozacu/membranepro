<?php

class Cybertech_Newsletterdownload_Model_Observer {

    const XML_PATH_EMAIL_TEMPLATE = 'newsletter_download_template';
	
	//const PRINTERS_ATTACHMENT_FILE = '/var/attachments/ghid-alegere-imprimanta-carduri.pdf';
	const PRINTERS_ATTACHMENT_NAME = '6 pași ca să economisești timp și bani când cumperi o imprimantă de carduri';
	const PRINTERS_ATTACHMENT_URL = 'http://www.printcard.ro/doc/Ghid-Printcard.pdf';
	
    public function newsletterDownload($observer) 
    {
		$eventname = $observer->getEvent()->getName();
		$subscriber = $observer->getEvent()->getSubscriber();
		$email = $subscriber->getEmail();
		$id = $subscriber->getId();
		
		$isSubscribed = $subscriber->isSubscribed();
		$prevId = Mage::registry('ct_nlp_prevoius_subscriber_id');
		//Mage::log("newsletter subscribe - entered hook".$email);

		$emailtemplate = Mage::getModel('core/email_template')->loadDefault(self::XML_PATH_EMAIL_TEMPLATE);
		$sender = array();
		//$sender['name'] = Mage::getStoreConfig('trans_email/ident_general/name');
		//$sender['email'] = Mage::getStoreConfig('trans_email/ident_general/email');
		$sender['name'] = Mage::getStoreConfig('trans_email/ident_custom1/name');
		$sender['email'] = Mage::getStoreConfig('trans_email/ident_custom1/email');

		$receiver_name = "Client";                  //Modify the receiver name and email here according to your needs
		$receiver_email = $email;

		if($isSubscribed && empty($prevId)) { // only trigger at subscription and if wasn't subscribed before
			try {
				//$fileContents = file_get_contents(Mage::getBaseDir().self::PRINTERS_ATTACHMENT_FILE);
				//$attachment = $emailtemplate->getMail()->createAttachment($fileContents,'application/pdf');
				//$attachment->filename = 'Ghid alegere imprimanta carduri.pdf';
					
				$emailtemplate->sendTransactional(
						self::XML_PATH_EMAIL_TEMPLATE,
						$sender,
						$receiver_email,
						$receiver_name,
						array(
								'documentName' => self::PRINTERS_ATTACHMENT_NAME,
								'documentUrl' => self::PRINTERS_ATTACHMENT_URL,
						)
				);
				//The below line shows a success message on successful subscription.
				echo Mage::getSingleton('core/session')->addSuccess("Ati primit un email cu instructiuni pentru descarcarea eBook-ului GRATUIT: ".self::PRINTERS_ATTACHMENT_NAME.".");
			} catch (Mage_Core_Exception $e) {
				echo $e->getMessage();
			}
		} // end if($isSubscribed && empty($prevId))
    }
    
    public function alreadySubscribed($observer)
    {
    	$subscriber = $observer->getEvent()->getSubscriber();
    	$id = $subscriber->getId();
    	if($id) {
    		Mage::unregister('ct_nlp_prevoius_subscriber_id');
    		Mage::register('ct_nlp_prevoius_subscriber_id', $id);
    	}
    }

}

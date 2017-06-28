<?php
 
class Cybertech_Contact_TehnicController extends Mage_Core_Controller_Front_Action
{
	const MAIL_ADDRESS_ORDERS = 'suport@printcard.ro';
	const MAIL_NAME_ORDERS = 'Printcard Systems - departament suport tehnic';
	const MAIL_SUBJECT_ORDERS = 'Formular catre Departamentul tehnic - printcard.ro';
	
    public function indexAction()
    {
        //Get current layout state
        $this->loadLayout();  
 
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'cybertech.contact_form_tehnic',
            array(
                'template' => 'contact-custom/contact-form-tehnic.phtml'
            )
        );
 
        $this->getLayout()->getBlock('content')->append($block);
        //$this->getLayout()->getBlock('right')->insert($block, 'catalog.compare.sidebar', true);
 
        $this->_initLayoutMessages('core/session');
 
        $this->renderLayout();
    }
 
    public function sendemailAction()
    {
        //Fetch submited params
        $params = $this->getRequest()->getParams();
 
        $messageContent = '
        	<table>
        		<tr>
        			<td width="150">Nume</td>
        			<td>'.$params['name'].'</td>
        		</tr>
        		<tr>
        			<td>Firma</td>
        			<td>'.$params['company'].'</td>
        		</tr>
        		<tr>
        			<td>Telefon</td>
        			<td>'.$params['phone'].'</td>
        		</tr>
        		<tr>
        			<td>E-mail</td>
        			<td>'.$params['email'].'</td>
        		</tr>
        		<tr>
        			<td>&nbsp;</td>
        			<td>&nbsp;</td>
        		</tr>
        		<tr>
        			<td>Model imprimanta</td>
        			<td>'.$params['model_imprimanta'].'</td>
        		</tr>
                <tr>
        			<td>Serie</td>
        			<td>'.$params['serie'].'</td>
        		</tr>
        		<tr>
        			<td>&nbsp;</td>
        			<td>&nbsp;</td>
        		</tr>
        		<tr>
        			<td colspan="2">Descriere solicitare:</td>
        		</tr>
        		<tr>
        			<td colspan="2">'.$params['message'].'</td>
        		</tr>
        	</table>
        ';

        
        $mail = new Zend_Mail();
        $mail->setBodyHtml($messageContent);
        $mail->setFrom($params['email'], $params['name']);
        $mail->addTo(self::MAIL_ADDRESS_ORDERS, self::MAIL_NAME_ORDERS);
        $mail->setSubject(self::MAIL_SUBJECT_ORDERS);
        try {
            $mail->send();
            Mage::getSingleton('core/session')->addSuccess('Solicitarea Dvs. a fost trimisa. Va vom contacta in cel mai scurt timp posibil. Va multumim!');
        }
        catch(Exception $ex) {
            Mage::getSingleton('core/session')->addError('E-mailul nu a putut fi trimis. Va rugam trimiteti un mail pe adresa '.MAIL_ADDRESS_ORDERS.'. Va multumim pentru intelegere!');
 
        }
 
        //Redirect back to index action of (this) inchoo-simplecontact controller
        $this->_redirect('contact/tehnic/');
    }
}
 

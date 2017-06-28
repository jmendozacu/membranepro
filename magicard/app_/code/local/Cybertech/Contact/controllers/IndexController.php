<?php
 
class Cybertech_Contact_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        //Get current layout state
        $this->loadLayout();  
 
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'cybertech.contact',
            array(
                'template' => 'contact-custom/contact.phtml'
            )
        );
 
        $this->getLayout()->getBlock('content')->append($block);
        //$this->getLayout()->getBlock('right')->insert($block, 'catalog.compare.sidebar', true);
 
        $this->_initLayoutMessages('core/session');
 
        $this->renderLayout();
    }
}
 

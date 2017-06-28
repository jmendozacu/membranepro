<?php
class Busteco_CautareRapida_ConsumabileController extends Mage_Core_Controller_Front_Action
{
    public function rezultateAction()
    {
        $this->loadLayout();     
        $this->getLayout()->getBlock('head')->setTitle($this->__('Rezultate cautare rapida consumabile'));
        
        $breadcrumbs = $this -> getLayout() -> getBlock("breadcrumbs");
        $breadcrumbs -> addCrumb("home", array(
                "label" => $this -> __("Home"), 
                "title" => $this -> __("Home"), 
                "link" => Mage::getBaseUrl()
            )
        );
        $breadcrumbs -> addCrumb("rezultate-cautare-rapida", 
            array(
                "label" => $this -> __("Rezultate cautare rapida consumabile"), 
                "title" => $this -> __("Rezultate cautare rapida consumabile")
            )
        );
        
        $this->renderLayout();
    }
    
}
<?php
//class Busteco_CautareRapida_Block_Rezultate extends Mage_Core_Block_Template
class Busteco_CautareRapida_Block_Rezultate extends Mage_Catalog_Block_Product_List
{
    //protected $_collection;
    protected $_productCollection;
    
    // public function __construct()
    // {
        // parent::__construct();
//         
        // $sku = str_replace('~', '/', $this->getRequest()->getParam('sku'));
//     
        // //$attributeValue = $_GET['type'];
        // $attributeLabel = str_replace('~', '/', $this->getRequest()->getParam('type'));
        // $attributeCode = 'consumable_type';
//         
        // // Get attribute value ID by label
        // $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')
                // ->setCodeFilter($attributeCode)
                // ->getFirstItem();
//                             
        // $attributeOptions = $attributeInfo->getSource()->getAllOptions(false);
        // //Zend_Debug::dump($attributeOptions); die();
//         
        // foreach ($attributeOptions as $id => $attributeOptionArr) {
            // if($attributeLabel == $attributeOptionArr['label']) {
                // $attributeOptionArr['label'];
                // $attributeValue = $attributeOptionArr['value'];
                // break;
            // }
        // }
//         
        // // Get all related products for SKU, and then filter by attribute value
        // $collection = Mage::getModel('catalog/product')
                            // ->loadByAttribute('sku', $sku)
                            // ->getRelatedProductCollection()
                            // ->addAttributeToSelect('*')
                            // ->addAttributeToFilter('status', 1)
                            // //->addAttributeToFilter($attributeCode, $attributeValue)
                            // ;
//                             
        // if ($attributeValue) {
            // $collection->addAttributeToFilter($attributeCode, $attributeValue);
        // }
//         
        // $this->_collection = $collection;
        // $this->setCollection($collection);  
    // }

    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            
            $sku = str_replace('~', '/', $this->getRequest()->getParam('sku'));
    
            //$attributeValue = $_GET['type'];
            $attributeLabel = str_replace('~', '/', $this->getRequest()->getParam('type'));
            $attributeCode = 'consumable_type';
            
            // Get attribute value ID by label
            $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')
                    ->setCodeFilter($attributeCode)
                    ->getFirstItem();
                                
            $attributeOptions = $attributeInfo->getSource()->getAllOptions(false);
            //Zend_Debug::dump($attributeOptions); die();
            
            foreach ($attributeOptions as $id => $attributeOptionArr) {
                if($attributeLabel == $attributeOptionArr['label']) {
                    $attributeOptionArr['label'];
                    $attributeValue = $attributeOptionArr['value'];
                    break;
                }
            }
            
            // Get all related products for SKU, and then filter by attribute value
            $collection = Mage::getModel('catalog/product')
                                ->loadByAttribute('sku', $sku)
                                ->getRelatedProductCollection()
                                ->addAttributeToSelect('*')
                                ->addAttributeToFilter('status', 1)
                                //->addAttributeToFilter($attributeCode, $attributeValue)
                                ;
                                
            if ($attributeValue) {
                $collection->addAttributeToFilter($attributeCode, $attributeValue);
            }
            
            Mage::getModel('catalog/layer')->prepareProductCollection($collection);
            $this->_productCollection = $collection;
        }
        return parent::_getProductCollection();
    }    
        
    // protected function _prepareLayout()
    // {
        // parent::_prepareLayout();
//  
        // $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        // $pager->setAvailableLimit(array(8=>8,12=>12,16=>16,20=>20, 'all'=>'all'));
        // $pager->setCollection($this->_collection);                
        // $this->setChild('pager', $pager);
        // $this->getCollection()->load();
        // return $this;
    // }
//  
    // public function getPagerHtml()
    // {
            // return $this->getChildHtml('pager');
    // }
}
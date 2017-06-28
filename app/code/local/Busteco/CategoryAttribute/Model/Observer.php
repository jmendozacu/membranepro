<?php
class Busteco_CategoryAttribute_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * @param $observer
     */
    public function addProductFilter($observer)
    {
        //$storeId = Mage::app()->getStore()->getStoreId();
        $storeCode = Mage::app()->getStore()->getCode();
        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        $childrens = $menu->getAllChildNodes();

        foreach ($childrens as $child) {
            $childId = explode('-', $child->getId());
            $childDetails = Mage::getModel('catalog/category')->load(end($childId));
            //Zend_Debug::dump($childDetails->getUrl());
           
            if ($childDetails->getIsActive() && $childDetails->getFilterProductsByAttribute()) {
                
                $attributeCodes = explode(',', $childDetails->getFilterProductsByAttribute());
                $excludeAttributes = array_map('trim', explode(',', $childDetails->getFilterProductsExcludeAttr()));
                $excludeAttributes = array_map('strtolower', $excludeAttributes);
                
                foreach ($attributeCodes as $attributeCode) {
                    
                    $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attributeCode);

                    if ($attribute->usesSource()) {
                        //Zend_Debug::dump(get_class_methods($attribute));
                        $storeId = $attribute->getStoreId();
                        $options = $attribute->setStoreId($storeId)->getSource()->getAllOptions(false);
                        
                        foreach ($options as $option) {
                            
                            // exclude unwanted attributes
                            if (in_array(strtolower($option['label']), $excludeAttributes)) {
                                continue;
                            }
                            
                            $products = Mage::getModel('catalog/product')->getCollection()
                                ->addAttributeToSelect('*')
                                ->addFieldToFilter('status', 1)
                                ->addAttributeToFilter('visibility', array('in' => array(2, 4))); // Only catalog and, search visiblity
                            $products->addCategoryFilter($childDetails);
                            $products->addAttributeToFilter($attributeCode, $option['value']);
                                
                            if ($products->getSize()) {
                                //Zend_Debug::dump($attribute->getFrontendInput());
                                $node = new Varien_Data_Tree_Node(array(
                                                 'name'   => $option['label'] . ' (' . $products->getSize() . ')',
                                                 'id'     => $child->getId() . '-' . $option['label'] . '-' . $option['value'],
                                                 'url'    => $child->getUrl() . '/' . $this->formatString($attribute->getStoreLabel($storeCode)) . '/' . $this->formatString($option['label']),
                                                 'class'  => 'attr-filter-' . $this->formatString($option['label']),
                                         ), 'id', $tree, $menu);
                                         
                                $child->addChild($node);
                            }
                        }
                    }
                }
            } 
        }
    } 

    public function formatString($string)
    {
        return str_replace(' ', '-', strtolower($string));
    }
}
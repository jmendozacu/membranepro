<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_Product extends Mage_Core_Model_Abstract
{
    /**
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function prepareCanonical($observer)
    {
        //catalog_product_save_before
        if(!Mage::app()->getRequest()->getParam('store')){
            return;
        }

        $product = $observer->getData('product');

        if(!is_object($product)){
            return;
        }

        if (!Mage::app()->getRequest()->getParam('canonical_url_custom')) {
            $this->_deleteOldCustomRewriteIfExists($product);
            return;
        }

        $storeId = Mage::app()->getRequest()->getParam('store');
        $hashId = str_replace('0.', '', str_replace(' ', '_', microtime()));

        try {
            Mage::getModel('core/url_rewrite')
                    ->setStoreId($storeId)
                    ->setCategoryId(null)
                    ->setProductId($product->getId())
                    ->setIdPath($hashId)
                    ->setRequestPath(Mage::app()->getRequest()->getParam('canonical_url_custom'))
                    ->setTargetPath($product->getUrlPath())
                    ->setIsSystem(0)
                    ->setOptions('RP')
                    ->save();
        }
        catch (Exception $e) {
           ///If "Request Path for Specified Store already exists"

            $obj = Mage::getModel('core/url_rewrite')->load(Mage::app()->getRequest()->getParam('canonical_url_custom'),
            'request_path');
            $hashId = $obj->getIdPath();
        }
        $product->setCanonicalUrl($hashId);
        $this->_deleteOldCustomRewriteIfExists($product);
    }

    /**
     * Add to excluded field list "canonical_url" attribute
     * Add source modules for "meta_robots" and "canonical_cross_domain" attributes
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function modifyProductAttributesInMassAction($observer)
    {
        $object = $observer->getObject();

        $excludeAttributes = $object->getFormExcludedFieldList();
        $excludeAttributes[] = 'canonical_url';
        $object->setFormExcludedFieldList($excludeAttributes);

        foreach($object->getAttributes() as $attribute){            
            if($attribute->getAttributeCode() == 'meta_robots'){
                $attribute->setSourceModel('seosuite/catalog_product_attribute_source_meta_robots');
            }

            if($attribute->getAttributeCode() == 'canonical_cross_domain'){
                $attribute->setSourceModel('seosuite/system_config_source_crossdomain');
            }
        }
    }

    /**
     * Retrive options for canonical URL attribute
     * 
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function modifyFormFieldForProductAttributes($observer)
    {
        //adminhtml_catalog_product_edit_prepare_form
        $form = $observer->getForm();
        $product = Mage::registry('current_product');

        if($product && !$product->getId()){
            foreach($form->getElements() as $fieldset){
                $fieldset->removeField('canonical_url');
            }
        }

        $canonical = $form->getElement('canonical_url');

        if ($canonical) {

            if(!Mage::app()->getRequest()->getParam('store')){
                $canonical->setRenderer(
                    Mage::app()->getLayout()->createBlock('seosuite/adminhtml_renderer_canonical')
                );
            }else{
                $canonical->setValues(Mage::getModel('seosuite/catalog_product_attribute_source_meta_canonical')->getAllOptions());

                $html = "
                    <div style='padding-top:5px;'>
                        <input type='text' value='' style='display:none; width:275px' name='canonical_url_custom' id='canonical_url_custom'>
                    </div>\n
                <script type='text/javascript'>
                function listenCU() {
                    if($('canonical_url').value=='custom') {
                        $('canonical_url_custom').show();
                    }
                    else {
                        $('canonical_url_custom').hide();
                    }
                }
                $('canonical_url').observe('change',listenCU);
                     </script>";

                $canonical->setAfterElementHtml($html);
            }
        }

        $metaRobots = $form->getElement('meta_robots');

        if($metaRobots){
            $metaRobots->setValues(Mage::getModel('seosuite/catalog_product_attribute_source_meta_robots')->getAllOptions());
        }

        $canonicalCrossDomain = $form->getElement('canonical_cross_domain');

        if($canonicalCrossDomain){
            $canonicalCrossDomain->setValues(Mage::getModel('seosuite/system_config_source_crossdomain')->getAllOptions());
        }
    }

    /**
     * Clear unused URL rewrites
     *
     * @param Mage_Catalog_Model_Product $product
     * @return void
     */
    protected function _deleteOldCustomRewriteIfExists($product)
    {
        $oldCanonicalCode = $product->getOrigData('canonical_url');
        $newCanonicalCode = $product->getData('canonical_url');

        if($oldCanonicalCode && strpos($oldCanonicalCode, '_') !== false && $oldCanonicalCode != $newCanonicalCode){
            list($num1, $num2) = explode('_', $oldCanonicalCode);
            if(!empty($num1) && !empty($num2) && is_numeric($num1) && is_numeric($num2)){
                $rewrite = Mage::getModel('core/url_rewrite')->loadByIdPath($product->getOrigData('canonical_url'));
                if($rewrite && $rewrite->getUrlRewriteId() &&
                   $rewrite->getStoreId() == $product->getStoreId() &&
                   $rewrite->getProductId() == $product->getId() ){
                   $rewrite->delete();
                }
            }
        }
    }
}
<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Observer_Product extends Mage_Core_Model_Abstract
{
    /**
     * Convert properties of the product that contain [category] and [categories]
     *
     * @param type $observer
     * @return type
     */
    public function updateProductProperties($observer)
    {
        $product = $observer->getData('product');

        if (Mage::helper('mageworx_seoxtemplates/config')->isUseProductSeoName() && $product->getProductSeoName()) {
            $product->setName($product->getProductSeoName());
        }

        $properties = array();

        $properties['metaTitle']        = $product->getMetaTitle();
        $properties['metaDescription']  = $product->getMetaDescription();
        $properties['metaKeyword']      = $product->getMetaKeyword();
        $properties['description']      = $product->getDescription();
        $properties['shortDescription'] = $product->getShortDescription();

        $categoryFlag   = false;
        $categoriesFlag = false;

        foreach ($properties as $property) {
            if (strpos($property, '[category]') !== false) {
                $categoryFlag = true;
            }
            if (strpos($property, '[categories]') !== false) {
                $categoryFlag   = true;
                $categoriesFlag = true;
                break;
            }
        }

        if (!$categoryFlag && !$categoriesFlag) {
            return;
        }

        $catData = $this->_getCatData($categoriesFlag);

        $metaTitle       = str_ireplace(array('[category]', '[categories]'), $catData, $product->getMetaTitle());
        $metaDescription = str_ireplace(array('[category]', '[categories]'), $catData, $product->getMetaDescription());

        if (Mage::helper('mageworx_seoxtemplates/config')->isCropMetaTitle()) {
            $lengthTitle = Mage::helper('mageworx_seoxtemplates/config')->getMaxLengthMetaTitle();
            $metaTitle   = trim(mb_substr($metaTitle, 0, $lengthTitle));
        }

        if (Mage::helper('mageworx_seoxtemplates/config')->isCropMetaDescription()) {
            $lengthDesc      = Mage::helper('mageworx_seoxtemplates/config')->getMaxLengthMetaDescription();
            $metaDescription = trim(mb_substr($metaDescription, 0, $lengthDesc));
        }

        $product->setMetaTitle($metaTitle);
        $product->setMetaDescription($metaDescription);

        $product->setMetaKeyword(str_ireplace(array('[category]', '[categories]'), $catData, $product->getMetaKeyword()));
        $product->setDescription(str_ireplace(array('[category]', '[categories]'), $catData, $product->getDescription()));
        $product->setShortDescription(str_ireplace(array('[category]', '[categories]'), $catData,
                $product->getShortDescription()));
    }

    /**
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $categoriesFlag
     * @return array
     */
    protected function _getCatData($categoriesFlag)
    {
        $controller = Mage::app()->getFrontController();
        if (is_object($controller) && is_callable(array($controller, 'getRequest'))) {
            $request = $controller->getRequest();
            if (is_object($request)) {
                $params = $request->getParams();
            }
        }
        if (empty($params['category'])) {
            return $this->_getDefaultCatData();
        }

        if (!is_callable(array(Mage::getResourceModel('catalog/category'), 'getAttributeRawValue'))) {
            return $this->_getDefaultCatData();
        }

        if ($categoriesFlag) {
            $path      = Mage::getResourceModel('catalog/category')->getAttributeRawValue($params['category'], 'path',
                Mage::app()->getStore()->getId());
            $pathArray = array_reverse(explode('/', $path));
            $names     = array();
            foreach ($pathArray as $id) {
                $value = Mage::getResourceModel('catalog/category')->getAttributeRawValue($id, 'name',
                    Mage::app()->getStore()->getId());
                if ($value && $value != 'Root Catalog') {
                    $names[$id] = $value;
                }
            }

            $categories = trim(implode(', ', $names));
            $category   = array_shift($names);
            return array('category' => $category, 'categories' => $categories);
        }
        else {
            $name = Mage::getResourceModel('catalog/category')->getAttributeRawValue($params['category'], 'name',
                Mage::app()->getStore()->getId());
            if ($name == 'Root Catalog') {
                $name = '';
            }
            return array('category' => $name, 'categories' => '');
        }
    }

    protected function _getDefaultCatData()
    {
        return array('category' => '', 'categories' => '');
    }

}

<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoXTemplates_Model_Converter_Category extends MageWorx_SeoXTemplates_Model_Converter
{
    protected function __convert($vars, $templateCode)
    {
        $convertValue = $templateCode;

        foreach ($vars as $key => $params) {
            foreach ($params['attributes'] as $attributeCode) {

                switch ($attributeCode) {
                    case 'category':
                        $value = $this->_convertName();
                        break;
                    case 'price':
                    case 'special_price':
                        break;
                    case 'parent_category':
                        $value = $this->_convertParentCategory();
                        break;
                    case 'categories':
                        $value = $this->_convertCategories();
                        break;
                    case 'store_view_name':
                        $value = $this->_convertStoreViewName();
                        break;
                    case 'store_name':
                        $value = $this->_convertStoreName();
                        break;
                    case 'website_name':
                        $value = $this->_convertWebsiteName();
                        break;
                    default:
                        $value = $this->_convertAttribute($attributeCode);
                        break;
                }

                if ($value) {
                    $value = $params['prefix'] . $value . $params['suffix'];
                    break;
                }
            }
            $convertValue = str_replace($key, $value, $convertValue);
        }

        return $this->_render($convertValue);
    }

    protected function _convertName()
    {
        return $this->_item->getName();
    }

    protected function _convertParentCategory()
    {
        $value = '';
        if ($this->_item->getParentId()) {
            $cat = Mage::getModel('catalog/category')->load($this->_item->getParentId());
            if ($cat->getId() !== Mage::app()->getWebsite(Mage::app()->getStore($this->_item->getStoreId())->getWebsite()->getId())->getDefaultStore()->getRootCategoryId()) {
                $value = $cat->getName();
            }
        }
        return $value;
    }

    protected function _convertCategories()
    {
        $value     = '';
        $separator = ' ' . Mage::helper('mageworx_seoxtemplates/config')->getTitleSeparator() . ' ';
        $paths     = explode('/', $this->_item->getPath());

        if (is_array($paths)) {
            array_shift($paths);
            if (Mage::helper('mageworx_seoxtemplates/config')->isCropRootCategory($this->_item->getStoreId())) {
                array_shift($paths);
            }
            foreach ($paths as $categoryId) {
                $category = Mage::getModel('catalog/category')->load($categoryId);
                $path[]   = trim($category->getName());
            }
        }
        else {
            $categories = $this->_item->getParentCategories();
            // add category path breadcrumb
            foreach ($categories as $categoryId) {
                $category = Mage::getModel('catalog/category')->load($categoryId->getId());
                $path[]   = trim($category->getName());
            }
        }

        if (!empty($path) && is_array($path) && count($path) > 0) {
            $path  = array_filter($path);
            $value = join($separator, array_reverse($path));
        }

        return $value;
    }

    protected function _convertStoreViewName()
    {
        return Mage::app()->getStore($this->_item->getStoreId())->getName();
    }

    protected function _convertStoreName()
    {
        return Mage::app()->getStore($this->_item->getStoreId())->getGroup()->getName();
    }

    protected function _convertWebsiteName()
    {
        return Mage::app()->getStore($this->_item->getStoreId())->getWebsite()->getName();
    }

    protected function _convertAttribute($attributeCode)
    {
        $value = '';
        if ($attribute = $this->_item->getResource()->getAttribute($attributeCode)) {
            $value = $attribute->getSource()->getOptionText($this->_item->getData($attributeCode));
        }
        if (!$value) {
            $value = $this->_item->getData($attributeCode);
        }
        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        return $value;
    }

    protected function _render($convertValue)
    {
        return trim($convertValue);
    }

}

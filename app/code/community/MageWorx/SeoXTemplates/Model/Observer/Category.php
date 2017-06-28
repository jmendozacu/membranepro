<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Observer_Category extends Mage_Core_Model_Abstract
{
    /**
     * Convert properties of the product that contain [category] and [categories]
     *
     * @param type $observer
     * @return type
     */
    public function updateCategoryProperties($observer)
    {
        $category = $observer->getData('category');

        if (Mage::helper('mageworx_seoxtemplates/config')->isUseCategorySeoName() && $category->getCategorySeoName()) {
            $category->setName($category->getCategorySeoName());
        }
    }

}

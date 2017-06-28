<?php
/**
* Magento
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@magentocommerce.com so we can send you a copy immediately.
*
* @category    Xj
* @package     Xj_Sticker
* @copyright   Copyright (c) 2012 Xj
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

class Xj_Sticker_Model_Product_Attribute_Source_Images
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $collection = $this->getEavImageAttributesCollection();
        /** @var $attribute Mage_Eav_Model_Entity_Attribute */
        foreach($collection as $attribute)
        {
            $options[] = array(
                'value'=> $attribute->getAttributeCode(),
                'label'=> $attribute->getFrontendLabel(),
            );
        }

        return $options;
    }

    /**
     * @param bool $allAttributes
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function getEavImageAttributesCollection($allAttributes = true)
    {
        if ($allAttributes) {
            $attributes = $this->_getHelper()->getAttributes();
        } else {
            $attributes = $this->_getHelper()->getAllowedAttributes();
        }

        $collection = $this->_getEavCollection();
        $collection->addFieldToFilter('attribute_code', array('in' => $attributes));
        $collection->addFieldToFilter('entity_type_id', $this->_getEntityTypeId());

        return $collection;
    }

    /**
     * @return Xj_Sticker_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('xj_sticker');
    }

    /**
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    protected function _getEavCollection()
    {
        return Mage::getResourceModel('eav/entity_attribute_collection');
    }

    /**
     * @return int
     */
    protected function _getEntityTypeId()
    {
        return (int) Mage::getModel('eav/entity_type')->load('catalog_product', 'entity_type_code')->getId();
    }
}
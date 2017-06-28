<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


/**
 * @see MageWorx_SeoMarkup_Model_Catalog_Product_Richsnippet_Product
 */
class MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Meta_Custom extends MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Abstract
{
    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        $string = $this->_getCustomPropertiesAsString();

        if (!empty($string)) {
            $node->innertext = $node->innertext . $string . "\n";
            return true;
        }
        return false;
    }

    protected function _getItemConditions()
    {
        return array("*[itemtype=http://schema.org/Product]");
    }

    protected function _checkBlockType()
    {
        return true;
    }

    protected function _isValidNode(simple_html_dom_node $node)
    {
        return true;
    }

    protected function _getCustomPropertiesAsString()
    {
        $customProperties = Mage::helper('mageworx_seomarkup/config')->getCustomProperties();

        if (count($customProperties)) {
            $customPropertyString = '';
            foreach ($customProperties as $customName => $customProperty) {
                $customPropertyString .= '<meta itemprop="' . $customName . '" content="'. $customProperty. '">' . "\n";
            }
            return trim($customPropertyString, "\n");
        }
        return null;
    }

}
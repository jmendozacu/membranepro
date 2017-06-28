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

class Xj_Sticker_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_NODE_CONFIG_ENABLED = 'xj_sticker/view/enabled';
    //const XML_NODE_CONFIG_ENABLED_PREGENERATE = 'xj_sticker/view/enabled_pregenerate';
    const XML_NODE_PATH_DEFAULT_ATTRIBUTES = 'default/xj_sticker/attributes';
    const XML_NODE_PATH_ALLOWED_ATTRIBUTES = 'xj_sticker/view/allowed_attributes';
    
    /**
     * @var array
     */
    protected $_allowedAttributes = null;

    /**
     * @return Xj_Sticker_Model_Sticker
     */
    public function getStickerItemInstance()
    {
        return Mage::registry('.sticker.item');
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) Mage::app()->getStore()->getConfig(self::XML_NODE_CONFIG_ENABLED);
    }

    /**
     * @return bool
     */
    /*
    public function isEnabledPregenerate()
    {
        return (bool) Mage::app()->getStore()->getConfig(self::XML_NODE_CONFIG_ENABLED_PREGENERATE);
    }
    */

    /**
     * @return array
     */
    public function getAllowedAttributes()
    {
        if (null === $this->_allowedAttributes) {
            $this->_allowedAttributes = array_filter(explode(',', Mage::app()->getStore()->getConfig(
                self::XML_NODE_PATH_ALLOWED_ATTRIBUTES)));
        }
        return $this->_allowedAttributes;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $attributes = (array) Mage::app()->getConfig()->getNode(self::XML_NODE_PATH_DEFAULT_ATTRIBUTES)->asArray();
        if (empty($attributes)) {
            return array();
        }
        return array_keys($attributes);
    }

    /**
     * @return string
     */
    public function getProductMediaPath()
    {
        return Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
    }

    /**
     * @param $path
     * @return array
     */
    public function getImageSize($path)
    {
        return getimagesize($path);
    }
}

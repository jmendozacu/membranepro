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

class Xj_Sticker_Model_Observer
{
    /**
     * @var Xj_Sticker_Model_Product_Image
     */
    protected $_imageItem = null;

    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_product = null;

    /**
     * @var array
     */
    protected $_productsToStickers = array();

    /**
     * @var array
     */
    protected $_attributes = array();

    public function __construct()
    {
        $this->_attributes = $this->_getHelper()->getAllowedAttributes();
    }

    /**
     * @param Varien_Object $observer
     * @return Xj_Sticker_Model_Observer
     */
    public function stickerSaveAfter($observer)
    {
        /** @var Xj_Sticker_Model_Sticker */
        $sticker = $observer->getEvent()->getSticker();
        if ($sticker->getId()) {
            $this->_saveStickerProducts($sticker);
            $this->_getItemModel()->clearCache();
        }
        return $this;
    }

    /**
     * @param Xj_Sticker_Model_Sticker $sticker
     * @return Xj_PremiumSticker_Model_Observer
     */
    protected function _saveStickerProducts(Xj_Sticker_Model_Sticker $sticker)
    {
        $products = $sticker->getStickerProducts();
        if (is_string($products) && !empty($products)) {
            $sticker->setProducts($this->_decode($products));
        } elseif (null !== $products) {
            $sticker->setProducts(array());
        } else {
            return $this;
        }

        if ($sticker->getId()) {
            $this->_getItemModel()->assignSelectedProducts($sticker);
        }
        return $this;
    }

    /**
     * @param Varien_Object $observer
     * @return Xj_Sticker_Model_Observer
     */
    public function catalogProductSaveAfter($observer)
    {
        /** @var Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getDataObject();
        if (!($product instanceof Mage_Catalog_Model_Product)) {
            return $this;
        }

        $stickers = $product->getXjStickers();
        if (is_string($stickers) && !empty($stickers)) {
            $product->setXjStickers($this->_decode($stickers));
        } elseif (null !== $stickers) {
            $product->setXjStickers(array());
        } else {
            return $this;
        }

        if ($product->getId()) {
            $this->_getItemModel()->assignStickersToProduct($product);
            $this->_getItemModel()->clearCache();
        }
        return $this;
    }

    /**
     * @param Varien_Object $observer
     * @return Xj_Sticker_Model_Observer
     */
    public function catalogProductLoadAfter($observer)
    {
        if (!$this->_getHelper()->isEnabled() || empty($this->_attributes)) {
            return $this;
        }

        /** @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getDataObject();
        //load and add stickers for current product
        if ($product && ($product instanceof Mage_Catalog_Model_Product) && $product->getId()) {
            $this->addProductsToStickers($this->getStickersDataByProduct($product->getId()));
        }
        return $this;
    }

    /**
     * @param Varien_Object $observer
     * @return Xj_Sticker_Model_Observer
     */
    public function catalogProductCollectionLoadAfter($observer)
    {
        if (!$this->_getHelper()->isEnabled() || empty($this->_attributes)) {
            return $this;
        }
        /** @var Mage_Catalog_Model_Resource_Product_Collection */
        $collection = $observer->getEvent()->getCollection();
        //load and add stickers for each product in collection
        if ($collection && ($collection instanceof Varien_Data_Collection) && count($collection->getAllIds())) {
            $this->addProductsToStickers($this->getStickersDataByProduct($collection->getAllIds()));
        }
        return $this;
    }

    /**
     * @param array|int $productIds
     * @return array|bool|mixed
     */
    public function getStickersDataByProduct($productIds)
    {
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }
        //load from cache stickers by products ids
        $stickers = $this->_getItemModel()->loadCacheByProductIds($productIds);
        if ($stickers === false) {//performance trick - check once
            $stickers = $this->_getStickersDataByProduct($productIds)->toMultiArray('product_id', 'sticker_id');
            $this->_getItemModel()->saveCacheByProductIds($stickers, $productIds);
        }
        return $stickers;
    }

    /**
     * Merge stickers array
     *
     * @param array $stickers
     * @return Xj_Sticker_Model_Observer
     */
    public function addProductsToStickers(array $stickers)
    {
        foreach($stickers as $productId => $pStickers) {
            foreach($pStickers as $stickerId => $data) {
                $this->_productsToStickers[$productId][$stickerId] = $data;
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getProductsToStickers()
    {
        return $this->_productsToStickers;
    }

    /**
     * @param $observer
     * @return Xj_Sticker_Model_Observer
     */
    public function catalogProductPrepareImageBefore($observer)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();

        /** @var $model Mage_Catalog_Model_Product_Image */
        $model = $observer->getEvent()->getModel();

        /** @var $helper Mage_Catalog_Helper_Image */
        $helper = $observer->getEvent()->getModel();

        $attribute = $model->getDestinationSubdir();
        $dir = $this->_getHelper()->getProductMediaPath();

        if (!$helper->getImageFile()
            && $this->_isAllowed($attribute)
            && ($product instanceof Mage_Catalog_Model_Product)
            && $product->getId()
            && !empty($this->_productsToStickers[$product->getId()])
            && $product->getData($attribute)
            && ($product->getData($attribute) != 'no_selection')
            && $this->_fileExists($dir . $product->getData($attribute))
        ) {

            $productId = $product->getId();
            $newAttribute = $this->_getNewAttribute($attribute, $this->_productsToStickers[$productId], 'p_' . $productId);
            $model->setDestinationSubdir($newAttribute);
            $product->setData($newAttribute, $product->getData($attribute));
        }
        return $this;
    }

    /**
     * @param $observer
     * @return Xj_Sticker_Model_Observer
     */
    public function catalogProductPrepareImageAfter($observer)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();

        /** @var $model Mage_Catalog_Model_Product_Image */
        $model = $observer->getEvent()->getModel();

        $newAttribute = $model->getDestinationSubdir();
        @list($attribute, $hash) = explode('_lst_', $newAttribute);

        //apply stickers to resized image
        if ($attribute && $hash
            && $model->getNewFile()
            && $this->_fileExists($model->getNewFile())
            && $this->_isAllowed($attribute)
            && ($product instanceof Mage_Catalog_Model_Product)
            && !empty($this->_productsToStickers[$product->getId()])
            && $product->getData($newAttribute)
        ) {
            try {
                $this->_imageItem = $this->_getImageItem();
                $this->_imageItem->setBaseFile($model->getNewFile());

                foreach($this->_productsToStickers[$product->getId()] as $sticker) {
                    $this->_addStickerWatermarkToImage($sticker, $attribute);
                }

                //re-save already re-sized image by catalog/image helper
                $this->_imageItem->saveFile();
            } catch(Exception $e) {
                Mage::logException($e);
            }
        }
        return $this;
    }

    /**
     * @param $attribute
     * @return bool
     */
    protected function _isAllowed($attribute)
    {
        return in_array($attribute, $this->_attributes);
    }

    /**
     * @param array $productIds
     * @return array
     */
    protected function _getStickersDataByProduct(array $productIds)
    {
        return $this->_getItemModel()->getCollection()
            ->useActive()
            ->setCollectAll(true)
            ->useProduct($productIds);
    }

     /**
     * @return Xj_Sticker_Model_Product_Image
     */
    protected function _getImageItem()
    {
        return Mage::getModel('xj_sticker/product_image');
    }

    /**
     * @return Xj_Sticker_Model_Sticker
     */
    protected function _getItemModel()
    {
        return Mage::getSingleton('xj_sticker/sticker');
    }

    /**
     * @return Xj_Sticker_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('xj_sticker');
    }

    /**
     * @param $input
     * @return mixed
     */
    protected function _decode($input)
    {
        return Mage::helper('adminhtml/js')->decodeGridSerializedInput($input);
    }

    /**
     * @param $file
     * @return bool
     */
    protected function _fileExists($file)
    {
        return file_exists($file);
    }

    /**
     * @param array $sticker
     * @param $attribute
     * @return Xj_Sticker_Model_Product_Image
     */
    protected function _addStickerWatermarkToImage(array $sticker, $attribute)
    {
        $position = isset($sticker["position"]) ? $sticker["position"] : null;
        $scale = !empty($sticker["scale_$attribute"]) ? $sticker["scale_$attribute"] : null;
        if(!empty($sticker["image"])) {
            $this->_imageItem->setStickerWatermark($sticker["image"], $position, null, null, null, $scale);
        }
        return $this;
    }

    /**
     * @param $attribute
     * @param array $stickers
     * @param $suffix
     * @return string
     */
    protected function _getNewAttribute($attribute, array &$stickers = array(), $suffix = '')
    {
        $str = '';
        foreach($stickers as $sticker) {
            if (isset($sticker['conditions_serialized'])) {
                unset($sticker['conditions_serialized']);
            }
            $str.= implode('_', $sticker);
        }

        $str.= $suffix;
        return $attribute . '_lst_' . $this->_getNewAttributeHash($str);
    }

    /**
     * @param $string
     * @return string
     */
    protected function _getNewAttributeHash($string)
    {
        return md5($string);
    }
}

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

/**
 * Sticker item model
 *
 * @author Xj
 */
class Xj_Sticker_Model_Sticker
    extends Mage_Core_Model_Abstract
    implements Xj_Core_Model_Item_Interface
{
    const DEFAULT_SCALE = 35;

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    const ERROR_IMAGE_SIZE = 'Wrong watermark size passed to field. Size should to have WxH format. Maximum 3 digits per each size (3 digits for width and 3 for height).';
    const CACHE_TAG = 'COLLECTION_DATA';
    const CACHE_TAG_STICKERS = 'XJ_STICKER_COLLECTION_DATA';

    protected $_imageDelete = null;
    protected $_eventPrefix = 'xj_sticker';
    protected $_eventObject = 'sticker';

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('xj_sticker/sticker');
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return Xj_Sticker_Model_Sticker
     */
    public function assignStickersToProduct($product)
    {
        $this->getResource()->assignStickersToProduct($product);
        return $this;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getSelectedStickersToProduct($product)
    {
        return $this->getResource()->getSelectedStickersToProduct($product);
    }

    /**
     * @return array
     */
    public function getAvailableStatuses()
    {
        return array(
            self::STATUS_ENABLED  => $this->_getHelper()->__('Enabled'),
            self::STATUS_DISABLED => $this->_getHelper()->__('Disabled'),
        );
    }

    /**
     * @return int
     */
    public function getStatusDisabled()
    {
        return self::STATUS_DISABLED;
    }

    /**
     * @return int
     */
    public function getStatusEnabled()
    {
        return self::STATUS_ENABLED;
    }

    /**
     * @return array
     */
    public function getAvailablePositions()
    {
        $options = array();
		$options['top-left']='Top/Left';
		$options['top-center']='Top/Center';
		$options['top-right']='Top/Right';
		$options['bottom-left']='Bottom/Left';
		$options['bottom-center']='Bottom/Center';
		$options['bottom-right']='Bottom/Right';
		$options['left-center']='Left/Center';
		$options['right-center']='Right/Center';
		$options['center']='Center';
        return $options;
		
    }

    /**
     * @return Mage_Adminhtml_Model_System_Config_Source_Watermark_Position
     */
    protected function _getWatermarkPositionModel()
    {
        return Mage::getSingleton('adminhtml/system_config_source_watermark_position');
    }

    /**
     * @return array
     */
    public function getAvailableScales()
    {
        
		$options['40']='40%';
        return $options;
    }

    /**
     * Filesystem directory path of sticker  images
     * relatively to media folder
     *
     * @return string
     */
    public function getMediaPath()
    {
        return 'xj/sticker';
    }

    /**
     * Filesystem full directory path of sticker  images
     * relatively to media folder
     *
     * @return string
     */
    public function getFullMediaPath()
    {
        return Mage::getBaseDir('media') . '/' . $this->getMediaPath();
    }

    /**
     * @return Xj_Sticker_Model_Sticker
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->_prepareImage();
        return $this;
    }

    /**
     * @return Xj_Sticker_Model_Sticker
     */
    protected function _beforeDelete()
    {
        $this->_imageDelete = $this->getImage();
        return $this;
    }

    /**
     * @return Xj_Sticker_Model_Sticker
     */
    protected function _afterDelete()
    {
        if ($this->_imageDelete) {
            $this->_deleteImage($this->_imageDelete);
        }
        return $this;
    }

    /**
     * @return Xj_Sticker_Model_Sticker
     */
    protected function _prepareImage()
    {
        $imageData = $this->getStickerImage();
        $isImageDeleted = (is_array($imageData) && !empty($imageData['delete']));

        if ($isImageDeleted) {
            $this->_deleteImage($this->getImage());
            $this->setImage(null);
        } elseif (!empty($_FILES['sticker_image']['name'])){
            if (null !== $this->getImage()) {
                $this->_deleteImage($this->getImage());
            }
            $this->setImage($this->_uploadImage('sticker_image'));
        }
        return $this;
    }

    /**
     * @param $key
     * @return string|null
     */
    protected function _uploadImage($key)
    {
        $image = null;
        if(empty($_FILES[$key]['name'])) {
            return null;
        }

        try {
            //prepare uploader to upload
            $uploader = new Varien_File_Uploader($key);
            $uploader->setAllowedExtensions(array('png')); //'gif', 'jpg', 'jpeg'
            $uploader->setAllowCreateFolders(true);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            // save the image to path
            $result = $uploader->save($this->getFullMediaPath());
            if (isset($result['file'])) {
                $image = $result['file'];
            }
        } catch (Exception $e) {
            Mage::logException($e);
			throw new Mage_Core_Exception($e->getMessage());
        }

        return $image;
    }

    /**
     * @param $image
     * @return bool
     */
    protected function _deleteImage($image)
    {
        $filename = $this->getFullMediaPath() . $image;
        if (file_exists($filename)) {
            $io = new Varien_Io_File();
            return $io->rm($filename);
        }
        return false;
    }

    /**
     * @return Xj_Sticker_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('xj_sticker');
    }

    /**
     * @return Xj_Sticker_Model_Sticker
     */
    protected function _afterSaveCommit()
    {
        $this->_clearCache();
        return $this;
    }

    /**
     * @return Xj_Sticker_Model_Sticker
     */
    protected function _afterDeleteCommit()
    {
        $this->_clearCache();
        return $this;
    }

    /**
     * @return Xj_Sticker_Model_Sticker
     */
    protected function _clearCache()
    {
        Mage::app()->getCacheInstance()->clean(array(self::CACHE_TAG_STICKERS));
        return $this;
    }

    /**
     * @return Xj_Sticker_Model_Sticker
     */
    public function clearCache()
    {
        return $this->_clearCache();
    }

    /**
     * @param array $ids
     * @return string
     */
    public function getCacheKeyByProductIds(array $ids)
    {
        return self::CACHE_TAG_STICKERS . '_products_'. implode('_', $ids);
    }

    /**
     * @param array $ids
     * @return bool|mixed
     */
    public function loadCacheByProductIds(array $ids = array())
    {
        if (empty($ids)) {
            return false;
        }
        $cacheKey = $this->getCacheKeyByProductIds($ids);
        if (Mage::app()->useCache('config') && $cache = Mage::app()->loadCache($cacheKey)) {
            return unserialize($cache);
        }
        return false;
    }

    /**
     * @param array $options
     * @param array $ids
     * @return bool|Xj_Sticker_Model_Sticker
     */
    public function saveCacheByProductIds(array $options, array $ids = array())
    {
        if (empty($ids)) {
            return false;
        }
        $cacheKey = $this->getCacheKeyByProductIds($ids);
        if (Mage::app()->useCache('config')) {
            Mage::app()->saveCache(serialize($options), $cacheKey, array(self::CACHE_TAG, self::CACHE_TAG_STICKERS));
        }
        return $this;
    }
}

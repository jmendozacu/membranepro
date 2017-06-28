<?php

/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Downloads extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */
class MageWorx_Downloads_Helper_Data extends Mage_Core_Helper_Abstract {
    const XML_DOWNLOADS_ENABLED = 'mageworx_cms/downloads/enabled';
    const XML_DOWNLOADS_DISPLAY_SIZE = 'mageworx_cms/downloads/display_size';
    const XML_DOWNLOADS_SIZE_PRECISION = 'mageworx_cms/downloads/size_precision';
    const XML_DOWNLOADS_DISPLAY_DOWNLOADS = 'mageworx_cms/downloads/display_downloads';
    const XML_DOWNLOADS_HIDE_FILES = 'mageworx_cms/downloads/hide_files';
    const XML_DOWNLOADS_PRODUCT_DOWNLOADS_TITLE = 'mageworx_cms/downloads/product_downloads_title';
    const XML_DOWNLOADS_FILE_DOWNLOADS_TITLE = 'mageworx_cms/downloads/file_downloads_title';
    const XML_DOWNLOADS_BLOCK_POSITION = 'mageworx_cms/downloads/block_position';
    const XML_DOWNLOADS_HOW_TO_DOWNLOAD_MESSAGE = 'mageworx_cms/downloads/how_to_download_message';
    const XML_DOWNLOADS_ENABLE_FILES_ON_CATEGORY_PAGES = 'mageworx_cms/downloads/enable_files_on_category_pages';
    const XML_DOWNLOADS_NAME_SIZE = 'mageworx_cms/downloads/name_size';

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    const ALLOW_GUESTS_CONFIG = 1;
    const ALLOW_GUESTS_ENABLED = 2;
    const ALLOW_GUESTS_DISABLED = 3;

    const DEFAULT_CATEGORY_ID = 1;

    protected $_urlHeaders = array();
    protected $_contentType = 'application/octet-stream';
    protected $_resourceFile = null;
    protected $_handle = null;

    public function getStatusArray() {
        return array(
            self::STATUS_ENABLED => $this->__('Enabled'),
            self::STATUS_DISABLED => $this->__('Disabled'),
        );
    }

    public function isDefaultCategoryId($id) {
        return (bool) (self::DEFAULT_CATEGORY_ID == $id);
    }

    public function isEnabled() {
        return Mage::getStoreConfigFlag(self::XML_DOWNLOADS_ENABLED);
    }

    public function isDisplaySize() {
        return Mage::getStoreConfigFlag(self::XML_DOWNLOADS_DISPLAY_SIZE);
    }
    
    public function getNameSize() {
        return Mage::getStoreConfig(self::XML_DOWNLOADS_NAME_SIZE);
    }
    
    public function isEnableFilesOnCategoryPages() {
        return Mage::getStoreConfigFlag(self::XML_DOWNLOADS_ENABLE_FILES_ON_CATEGORY_PAGES);
    }

    public function isHideFiles() {
        return Mage::getStoreConfigFlag(self::XML_DOWNLOADS_HIDE_FILES);
    }

    public function getSizePrecision() {
        return Mage::getStoreConfig(self::XML_DOWNLOADS_SIZE_PRECISION);
    }

    public function isDisplayDownloads() {
        return Mage::getStoreConfigFlag(self::XML_DOWNLOADS_DISPLAY_DOWNLOADS);
    }

    public function getProductDownloadsTitle() {
        $title = trim(Mage::getStoreConfig(self::XML_DOWNLOADS_PRODUCT_DOWNLOADS_TITLE));
        if (empty($title)) {
            $title = $this->__('Product Downloads');
        }
        return $title;
    }

    public function getHowToDownloadMessage() {
        $title = trim(Mage::getStoreConfig(self::XML_DOWNLOADS_HOW_TO_DOWNLOAD_MESSAGE));
        if (empty($title)) {
            $title = $this->__('Register to download this file');
        }
        return $title;
    }

    public function getFileDownloadsTitle() {
        $title = trim(Mage::getStoreConfig(self::XML_DOWNLOADS_FILE_DOWNLOADS_TITLE));
        if (empty($title)) {
            $title = $this->__('File Downloads');
        }
        return $title;
    }

    public function getBlockPosition() {
        return explode(',', Mage::getStoreConfig(self::XML_DOWNLOADS_BLOCK_POSITION));
    }

    public function isOldVersion() {
        $result = false;
        $version = Mage::getVersion();
        if (version_compare($version, '1.2.1', '<')) {
            $result = true;
        }
        return $result;
    }

    public function isEnterprise() {
        $res = false;
        if (version_compare(Mage::getVersion(), '1.4.0', '>=')) {
            $res = true;
        }
        return $res;
    }

    public function getFilter($data) {
        $result = array();
        $filter = new Zend_Filter();
        $filter->addFilter(new Zend_Filter_StringTrim());
//       	$filter->addFilter(new Zend_Filter_StripTags());

        if ($data) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $result[$key] = $this->getFilter($value);
                } else {
                    $result[$key] = $filter->filter($value);
                }
            }
        }
        return $result;
    }

    public function getDateFormat() {
        return Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }

    public function getFiles($path) {
        return @glob($path . "*.*");
    }

    public function getDownloadsPath($fileId) {
        return Mage::getBaseDir('media') . DS . 'downloads' . DS . $fileId . DS;
    }
    
    public function isDownloadsFile($fileId) {
        return $this->getFiles($this->getDownloadsPath($fileId));
    }

    public function prepareFileName($string) {
        $result = preg_replace(array('/[^\w]/i', '/[ ]{2,}/', '/[ ]/', '/[_]{1,}/'), array(' ', ' ', '_', '_'), Mage::helper('catalog/product_url')->format($string));
        $result = trim($result, '_');
        return strtolower($result);
    }

    public function getFileName(Varien_Object $item) {
        return $this->prepareFileName($item->getName()) . '.' . strtolower($item->getType());
    }

    protected function _getHandle() {
        if (!$this->_resourceFile) {
            Mage::throwException($this->__('Please set resource file and link type'));
        }
        if (is_null($this->_handle)) {
            $this->_handle = new Varien_Io_File();
            $this->_handle->open(array('path' => Mage::getBaseDir('var')));
            if (!$this->_handle->fileExists($this->_resourceFile, true)) {
                Mage::throwException($this->__('File does not exist'));
            }
            $this->_handle->streamOpen($this->_resourceFile, 'r');
        }
        return $this->_handle;
    }

    public function getFileType($filePath) {
        $ext = substr($filePath, strrpos($filePath, '.') + 1);
        $type = Mage::getConfig()->getNode('global/mime/types/x' . $ext);
        if ($type) {
            return $type;
        }
        return $this->_contentType;
    }

    public function getContentType() {
        $this->_getHandle();
        if (function_exists('mime_content_type')) {
            return mime_content_type($this->_resourceFile);
        } else {
            return $this->getFileType($this->_resourceFile);
        }
        return $this->_contentType;
    }

    public function output() {
        $handle = $this->_getHandle();
        while ($buffer = $handle->streamRead()) {
            print $buffer;
        }
    }

    public function processDownload($resource, Varien_Object $item) {
        $this->_resourceFile = $resource;

        $response = Mage::app()->getResponse();
        $response->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-type', $this->getContentType(), true);

        if ($fileSize = $this->_getHandle()->streamStat('size')) {
            $response->setHeader('Content-Length', $fileSize);
        }
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $this->getFileName($item));
        $response->clearBody();
        $response->sendHeaders();

        $this->output();
    }

    public function prepareFileSize($size) {
        $parsedSize = 0;
        $type = '';
        $round = 1;
        $b = $this->__('B');
        $kb = $this->__('KB');
        $mb = $this->__('MB');
        $kbSize = 1024;
        $mbSize = 1024 * 1024;

        switch ($this->getSizePrecision()) {
            case MageWorx_Downloads_Model_Filesize::FILE_SIZE_PRECISION_AUTO:
                if ($size >= $kbSize && $size < $mbSize) {
                    $parsedSize = $size / $kbSize;
                    $type = $kb;
                } elseif ($size >= $mbSize) {
                    $parsedSize = $size / $mbSize;
                    $type = $mb;
                } else {
                    $parsedSize = $size;
                    $type = $b;
                    $round = 0;
                }
                break;

            case MageWorx_Downloads_Model_Filesize::FILE_SIZE_PRECISION_MEGA:
                $parsedSize = $size / $mbSize;
                $type = $mb;
                $round = 2;
                break;

            case MageWorx_Downloads_Model_Filesize::FILE_SIZE_PRECISION_KILO:
                $parsedSize = $size / $kbSize;
                $type = $kb;
                break;

            default:
                $parsedSize = $size;
                $type = $b;
                $round = 0;
                break;
        }
        return round($parsedSize, $round) . ' ' . $type;
    }

    public function isLoggedIn() {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    public function checkCustomerAccess($data) {
        if (!$this->isHideFiles()) {
            return true;
        }
        $access = false;
        foreach ($data as $item) {
            $checkAccess = $this->checkCustomerGroupAccess($item);
            if ($checkAccess === true) {
                $access = true;
                break;
            }
        }
        return $access;
    }

    public function checkCustomerGroupAccess(Varien_Object $item) {
        $access = true;
        if ($item->getIsActive() == self::STATUS_DISABLED) {
            return false;
        }
        $groups = $item->getCustomerGroups();
        if (is_string($groups)) {
            $groups = explode(',', $groups);
        }
        if (empty($groups)) {
            $access = false;
        } else {
            if (is_array($groups) && !in_array(Mage::getSingleton('customer/session')->getCustomerGroupId(), $groups)) {
                $access = false;
            }
        }

        $limit = $item->getDownloadsLimit();
        if ($limit) {
            if ($item->getDownloads() >= $limit) {
                $access = false;
            }
        }
        return $access;
    }

    protected function _prepareIconPath($path) {
        return ltrim(str_replace(DS, '/', $path), '/');
    }

    protected function _getIconsPath($iconName) {
        return DS . 'images' . DS . 'downloads' . DS . 'types' . DS . $iconName;
    }

    public function getIconPath($iconName) {
        return Mage::getBaseDir('media') . $this->_getIconsPath($iconName);
    }

    public function getIconUrl($name) {
        $iconName = strtolower($name) . '.png';
        $filePath = $this->getIconPath($iconName);
        if (!file_exists($filePath)) {
            $iconName = 'default.png';
        }
        return Mage::getBaseUrl('media') . $this->_prepareIconPath($this->_getIconsPath($iconName)); //Mage::getDesign()->getSkinUrl($this->_prepareIconPath($this->_getIconsPath($iconName)), array('_area' => 'frontend'));
    }

    public function getIcon($name = null) {
        if ($name) {
            return '<img src="' . $this->getIconUrl($name) . '" />';
        }
    }

    public function getDownloadLink(Varien_Object $item) {        if ($item->getType() != 'pdf') {
            return Mage::getUrl('downloads/dl/file/', array('id' => $item->getFileId())) . $this->getFileName($item);
        }

        $files = $this->getFiles($this->getDownloadsPath($item->getId()));
        if (!empty($files)) {
            $fileParts = explode('/', $files[0]);

            $url = Mage::getStoreConfig('web/unsecure/base_url');
            $url = substr($url, 0, strlen($url)-1);
            $key = array_search('media', $fileParts);
            for ($i = $key; $i < count($fileParts); $i++) {
                $url .= '/' . $fileParts[$i];
            }
            return $url;
        }

        return '';
    }
    
    public function getEmbedLink(Varien_Object $item) {
        return Mage::getUrl('downloads/dl/getEmbedCode', array('id' => $item->getFileId()));
    }        public function getRelation($productId)    {        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();        $select = $connection->select()->from($tablePrefix . 'downloads_relation');        $rows = $connection->fetchAll($select);                foreach ($rows as $row) {            if ($row['product_id'] == $productId && $row['file_id']) {                return $row['file_id'];            }        }    }        public function getAllRelations($productId)    {        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();        $select = $connection->select()->from($tablePrefix . 'downloads_relation');        $rows = $connection->fetchAll($select);        $relations = array();                foreach ($rows as $row) {            if ($row['product_id'] == $productId && $row['file_id']) {                $relations[] = $row['file_id'];            }        }        return $relations;    }
}
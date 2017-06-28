<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Model_System_Config_Backend_Robots extends Mage_Core_Model_Config_Data
{

    private $_filePath;

    public function _construct()
    {
        $this->_filePath = Mage::getBaseDir() . '/' . 'robots.txt';

        parent::_construct();
    }

    public function afterLoad()
    {
        $file = $this->_filePath;
        if (!file_exists($file)) {
            $this->createRobotsFile();
        }
        return $this->setValue(@file_get_contents($file));
    }

    public function save()
    {
        $file       = $this->_filePath;
        $data       = Mage::app()->getRequest()->getParam('groups');
        $robotsData = ' ';
        if (isset($data['seosuite']['fields']['robots_editor'])) {
            $robotsData = $data['seosuite']['fields']['robots_editor'];
            $robotsData = $robotsData['value'];
        }
        if (!file_exists($file)) {
            if (!$this->createRobotsFile()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('seosuite')->__('File robots.txt can\'t be created. Please check permissions'));
            }
        }
        //	echo "<pre>"; var_dump($robotsData);// exit;
        if ($robotsData == "") {
            unlink($file);
            return true;
        }
        $result = file_put_contents($file, $robotsData, LOCK_EX);
        if ($result) {
            return true;
        }
        return Mage::getSingleton('adminhtml/session')->addError(Mage::helper('seosuite')->__("File robots.txt can't be modified. Please check permissions."));
    }

    public function createRobotsFile()
    {
        $file = $this->_filePath;
        if (!is_writable(Mage::getBaseDir())) {
            //Mage::getSingleton('adminhtml/session')->addError(Mage::helper('seosuite')->__('Root catalog not writeble. Please check permissions'));
            return false;
        }
        try {
            $handle = fopen($file, 'w+');
            fwrite($handle, '');
            fclose($handle);
//            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('seosuite')->__("File robots.txt was created."));
            return true;
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('seosuite')->__($e->getMessage()));
            return false;
        }
    }

}

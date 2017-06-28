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
 * Sticker admin edit form main tab block
 *
 * @author Xj
 */
class Xj_Sticker_Block_Adminhtml_Sticker_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_idPrefix = 'sticker_main_';

    /**
     * @return Xj_Sticker_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('xj_sticker');
    }

    /**
     * @return Xj_Sticker_Helper_Admin
     */
    protected function _getAclHelper()
    {
        return Mage::helper('xj_sticker/admin');
    }

    /**
     * @return Xj_Sticker_Helper_Admin
     */
    protected function _getAttributesCollection()
    {
        return Mage::getModel('xj_sticker/product_attribute_source_images')->getEavImageAttributesCollection();
    }

    /**
     * Prepare form elements for tab
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
    	/* @var $model Xj_Sticker_Model_Sticker */
        $model = $this->_getHelper()->getStickerItemInstance();

        /* @var $helper Xj_Sticker_Helper_Admin */
        $helper = $this->_getAclHelper();

        /**
         * Checking if user have permissions to save information
         */
        if ($helper->isActionAllowed('manage_sticker/save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('sticker_main_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => $this->_getHelper()->__('Sticker Details')
        ));

        $this->_addElementTypes($fieldset);

        if ($model->getId()) {
            $fieldset->addField('sticker_id', 'hidden', array(
                'name' => 'id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name'     => 'title',
            'label'    => $this->_getHelper()->__('Name'),
            'title'    => $this->_getHelper()->__('Sticker Name'),
            'required' => true,
            'disabled' => $isElementDisabled,
        ));

        $fieldset->addField('is_active', 'select', array(
            'name'     => 'is_active',
            'label'    => $this->_getHelper()->__('Active'),
            'title'    => $this->_getHelper()->__('Is Sticker Active?'),
            'required' => true,
            'options'  => $model->getAvailableStatuses(),
            'style'    => 'width: 200px',
            'disabled' => $isElementDisabled,
        ));

        $fieldset->addField('image', 'image', array(
            'name'      => 'sticker_image',
            'label'    => $this->_getHelper()->__('Upload Image (*.png)'),
            'comment'   => $this->_getHelper()->__('Upload image with transparent background.<br />Please upload an image of 128px * 128px for best result.'),
            'required' => true,
            'disabled' => $isElementDisabled,
            'style'    => 'width: 200px;',
			'onchange' => 'readURL(this);'
        ));

        $fieldset->addField('position', 'select', array(
            'name'     => 'position',
            'label'    => $this->_getHelper()->__('Position to Displat'),
            'title'    => $this->_getHelper()->__('Position on default image'),
            'required' => true,
            'options'  => $model->getAvailablePositions(),
            'disabled' => $isElementDisabled,
            'style'    => 'width: 200px;',
			'onchange' => 'rut_postion(this.value);'
        ));

        //Scales for image attributes
        foreach($this->_getAttributesCollection() as $attribute) {
            //$comment = 'Watermark size (Ex. 20% to Original Image)';
            $fieldset->addField('scale_' . $attribute->getAttributeCode(), 'select', array(
                'name'     => 'scale_' . $attribute->getAttributeCode(),
                //'label'    => $this->_getHelper()->__('Size for "%s"', $attribute->getFrontendLabel()),
                'title'    => $this->_getHelper()->__('Size for "%s"', $attribute->getFrontendLabel()),
                'options'  => $model->getAvailableScales(),
                'disabled' => $isElementDisabled,
                'style'    => 'width: 50px; display: none;',
                //'after_element_html' => '<div> ' . $this->_getHelper()->__($comment) . '</div>',
            ));
        }

        Mage::dispatchEvent('adminhtml_sticker_edit_tab_main_prepare_form', array('form' => $form));

        $form->setValues($model->getData());
        $this->setForm($form);

        $this->_prepareImage();

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return $this->_getHelper()->__('Sticker Details');
    }

    public function getTabTitle()
    {
        return $this->_getHelper()->__('Sticker Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareImage()
    {
        /** @var $element Varien_Data_Form_Element_Image */
        $element = $this->getForm()->getElement('image');
        if ($element->getValue()) {
            /* @var $model Xj_Sticker_Model_Sticker */
            $model = Mage::getSingleton('xj_sticker/sticker');
            $element->setValue($model->getMediaPath() . $element->getValue());
        }
    }

    /**
     * Retrieve predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('xj_sticker/adminhtml_renderer_image')
        );
    }
}

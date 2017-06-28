<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpoints Earning Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Block_Adminhtml_Earning_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_RewardPoints_Block_Adminhtml_Earning_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        if (Mage::getSingleton('adminhtml/session')->getFormData()) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData();
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        } elseif (Mage::registry('rate_data')) {
            $data = Mage::registry('rate_data')->getData();
        }
        $fieldset = $form->addFieldset('rewardpoints_form', array(
            'legend'=>Mage::helper('rewardpoints')->__('Rate information')
        ));
        
        $dataObj = new Varien_Object($data);
        Mage::dispatchEvent('rewardpoints_adminhtml_earning_rate_form', array(
            'tab_form'  => $this,
            'form'  => $form,
            'data'  => $dataObj,
        ));
        $data = $dataObj->getData();
        
        $fieldset->addField('money', 'text', array(
            'name'  => 'money',
            'label' => Mage::helper('rewardpoints')->__('Money spent for order'),
            'title' => Mage::helper('rewardpoints')->__('Money spent for order'),
            'required' => true,
            'after_element_html'    => '<strong>[' . Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE) . ']</strong>',
        ));
        
        $fieldset->addField('points', 'text', array(
            'label' => Mage::helper('rewardpoints')->__('Points earned'),
            'title' => Mage::helper('rewardpoints')->__('Points earned'),
            'required' => true,
            'name' => 'points',
            'note'=> Mage::helper('rewardpoints')->__('E.g. When "Money spent for order" is 10, "Points earned" is 1, if Customers spend 30 ($), they will receive 3 points.'),
        ));
        
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('website_ids', 'multiselect', array(
                'name' => 'website_ids[]',
                'label' => Mage::helper('rewardpoints')->__('Websites'),
                'title' => Mage::helper('rewardpoints')->__('Websites'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_config_source_website')->toOptionArray(),
            ));
        } else {
            $fieldset->addField('website_ids', 'hidden', array(
                'name' => 'website_ids[]',
                'value' => Mage::app()->getStore(true)->getWebsiteId()
            ));
            $data['website_ids'] = Mage::app()->getStore(true)->getWebsiteId();
        }
        
        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'label' => Mage::helper('rewardpoints')->__('Customer groups'),
            'title' => Mage::helper('rewardpoints')->__('Customer groups'),
            'name' => 'customer_group_ids',
            'required' => true,
            'values' => Mage::getResourceModel('customer/group_collection')
                ->addFieldToFilter('customer_group_id', array('gt' => 0))
                ->load()
                ->toOptionArray()
        ));
        
        $fieldset->addField('sort_order', 'text', array(
            'label' => Mage::helper('rewardpoints')->__('Priority'),
            'label' => Mage::helper('rewardpoints')->__('Priority'),
            'required' => false,
            'name' => 'sort_order',
            'note'=> Mage::helper('rewardpoints')->__('Higher priority Rate will be applied first')
        ));
        
        $form->setValues($data);
        return parent::_prepareForm();
    }
}

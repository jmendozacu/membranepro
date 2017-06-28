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
 * Sticker Sticker admin edit form container
 *
 * @author Xj
 */
class Xj_Sticker_Block_Adminhtml_Sticker_Edit
	extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize edit form container
     *
     */
    public function __construct()
    {
        $this->_objectId   = 'id';        
        $this->_blockGroup = 'xj_sticker';
        $this->_controller = 'adminhtml_sticker';

        parent::__construct();

        //check permissions
        if (Mage::helper('xj_sticker/admin')->isActionAllowed('manage_sticker/save')) {
            $this->_updateButton('save', 'label', Mage::helper('xj_sticker')->__('Save Sticker'));
            $this->_addButton('saveandcontinue', array(
                'label'   => Mage::helper('adminhtml')->__('Save Sticker and Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ), -100);

            $this->_formScripts[] = "
            	function saveAndContinueEdit(){
            		editForm.submit($('edit_form').action+'back/edit/');
            	}";
        } else {
            $this->_removeButton('save');
        }

        if (Mage::helper('xj_sticker/admin')->isActionAllowed('manage_sticker/delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('xj_sticker')->__('Delete Sticker Item'));
        } else {
            $this->_removeButton('delete');
        }
    }

    public function getHeaderText()
    {
    	$header = Mage::helper('xj_sticker')->__('Create New Sticker');
        $model = Mage::helper('xj_sticker')->getStickerItemInstance();
        
        if ($model->getId()) {
        	$title = $this->escapeHtml($model->getTitle());
            $header = Mage::helper('xj_sticker')->__("Edit Sticker '%s'", $title);
        }        
        return $header;
    }
}

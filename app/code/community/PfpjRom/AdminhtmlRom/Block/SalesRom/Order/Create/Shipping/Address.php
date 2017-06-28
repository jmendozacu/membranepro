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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   PfpjRom
 * @package    PfpjRom_AdminhtmlRom
 * @copyright  Copyright (c) Daniel Ifrim
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Overrides Adminhtml sales order create shipping address block
 *
 * @author     Daniel Ifrim
 */

class PfpjRom_AdminhtmlRom_Block_SalesRom_Order_Create_Shipping_Address extends PfpjRom_AdminhtmlRom_Block_SalesRom_Order_Create_Form_Address
{
	public function getHeaderText()
    {
        return Mage::helper('sales')->__('Shipping Address');
    }

    public function getHeaderCssClass()
    {
        return 'head-shipping-address';
    }

    protected function _prepareForm()
    {
        if (!$this->_form) {
            parent::_prepareForm();
            $this->_form->addFieldNameSuffix('order[shipping_address]');
            $this->_form->setHtmlNamePrefix('order[shipping_address]');
            $this->_form->setHtmlIdPrefix('order-shipping_address_');
        }
        return $this;
    }

    public function getIsShipping()
    {
        return true;
    }

    public function getIsAsBilling()
    {
        return $this->getCreateOrderModel()->getShippingAddress()->getSameAsBilling();
    }

    public function getFormValues()
    {
        return $this->getCreateOrderModel()->getShippingAddress()->getData();
    }


    public function getAddressId()
    {
        return $this->getCreateOrderModel()->getShippingAddress()->getCustomerAddressId();
    }

    public function getAddress()
    {
        return $this->getCreateOrderModel()->getShippingAddress();
    }

    public function getIsDisabled()
    {
        return $this->getQuote()->isVirtual();
    }

/* [start] PfpjRom edit */

    public function getAddressCollection()
    {
    	$addresses = $this->getCustomer()->getAddresses();
    	$shipping_addresses = $addresses;
    	foreach ($addresses as $key => $address) {
    		if ($address->getPfpjForShipping() != 1) {
    			unset($shipping_addresses[$key]);
    		}
    	}
        return $shipping_addresses;
    }

/* [end] PfpjRom edit */

}
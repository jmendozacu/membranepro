<?php
require_once 'Mage/Sales/Model/Order/Invoice/Item.php';

/**
 * Overrides the register() method
 * to generate invoices in RON
 * from Magento 1.6.0
 */
class Cybertech_Sales_Model_Order_Invoice_Item extends Mage_Sales_Model_Order_Invoice_Item
{
	const newCurrencyCode = 'RON';
	
    /**
     * Applying qty to order item
     *
     * @return Mage_Sales_Model_Order_Invoice_Item
     */
    public function register()
    {
        $orderItem = $this->getOrderItem();
        $orderItem->setQtyInvoiced($orderItem->getQtyInvoiced()+$this->getQty());

        $orderItem->setTaxInvoiced($orderItem->getTaxInvoiced()+$this->getTaxAmount());
        $orderItem->setBaseTaxInvoiced($orderItem->getBaseTaxInvoiced()+$this->getBaseTaxAmount());
        $orderItem->setHiddenTaxInvoiced($orderItem->getHiddenTaxInvoiced()+$this->getHiddenTaxAmount());
        $orderItem->setBaseHiddenTaxInvoiced($orderItem->getBaseHiddenTaxInvoiced()+$this->getBaseHiddenTaxAmount());

        $orderItem->setDiscountInvoiced($orderItem->getDiscountInvoiced()+$this->getDiscountAmount());
        $orderItem->setBaseDiscountInvoiced($orderItem->getBaseDiscountInvoiced()+$this->getBaseDiscountAmount());

        $orderItem->setRowInvoiced($orderItem->getRowInvoiced()+$this->getRowTotal());
        $orderItem->setBaseRowInvoiced($orderItem->getBaseRowInvoiced()+$this->getBaseRowTotal());
        
        // RON Begin
        $orderItem->setOriginalPrice(Mage::helper('directory')->currencyConvert($orderItem->getOriginalPrice(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        /* $orderItem->setPrice(Mage::helper('directory')->currencyConvert($orderItem->getPrice(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setOriginalPrice(Mage::helper('directory')->currencyConvert($orderItem->getOriginalPrice(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setTaxAmount(Mage::helper('directory')->currencyConvert($orderItem->getTaxAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setTaxInvoiced(Mage::helper('directory')->currencyConvert($orderItem->getTaxInvoiced(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setDiscountAmount(Mage::helper('directory')->currencyConvert($orderItem->getDiscountAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setDiscountInvoiced(Mage::helper('directory')->currencyConvert($orderItem->getDiscountInvoiced(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setAmountRefunded(Mage::helper('directory')->currencyConvert($orderItem->getAmountRefunded(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setRowTotal(Mage::helper('directory')->currencyConvert($orderItem->getRowTotal(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setRowInvoiced(Mage::helper('directory')->currencyConvert($orderItem->getRowInvoiced(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setTaxBeforeDiscount(Mage::helper('directory')->currencyConvert($orderItem->getTaxBeforeDiscount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setPriceInclTax(Mage::helper('directory')->currencyConvert($orderItem->getPriceInclTax(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setRowTotalInclTax(Mage::helper('directory')->currencyConvert($orderItem->getRowTotalInclTax(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setHiddenTaxAmount(Mage::helper('directory')->currencyConvert($orderItem->getHiddenTaxAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setHiddenTaxInvoiced(Mage::helper('directory')->currencyConvert($orderItem->getHiddenTaxInvoiced(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setHiddenTaxRefunded(Mage::helper('directory')->currencyConvert($orderItem->getHiddenTaxRefunded(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setTaxCanceled(Mage::helper('directory')->currencyConvert($orderItem->getTaxCanceled(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setHiddenTaxCanceled(Mage::helper('directory')->currencyConvert($orderItem->getHiddenTaxCanceled(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setTaxRefunded(Mage::helper('directory')->currencyConvert($orderItem->getTaxRefunded(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setBaseWeeeTaxAppliedAmount(Mage::helper('directory')->currencyConvert($orderItem->getBaseWeeeTaxAppliedAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setBaseWeeeTaxAppliedRowAmount(Mage::helper('directory')->currencyConvert($orderItem->getBaseWeeeTaxAppliedRowAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setBaseWeeeTaxDisposition(Mage::helper('directory')->currencyConvert($orderItem->getBaseWeeeTaxDisposition(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $orderItem->setBaseWeeeTaxRowDisposition(Mage::helper('directory')->currencyConvert($orderItem->getBaseWeeeTaxRowDisposition(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode)); */
        // RON End
        
        return $this;
    }

}

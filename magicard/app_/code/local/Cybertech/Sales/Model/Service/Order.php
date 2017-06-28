<?php
require_once 'Mage/Sales/Model/Service/Order.php';

/**
 * Overrides the prepareInvoice() method
 * to generate invoices in RON
 * from Magento 1.6.0
 */
class Cybertech_Sales_Model_Service_Order extends Mage_Sales_Model_Service_Order
{
	const newCurrencyCode = 'RON';
    /**
     * Prepare order invoice based on order data and requested items qtys
     *
     * @param array $data
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function prepareInvoice($qtys = array())
    {
    	$this->_order->setOrderCurrencyCode(self::newCurrencyCode);
        $invoice = $this->_convertor->toInvoice($this->_order);
        $totalQty = 0;
        foreach ($this->_order->getAllItems() as $orderItem) {
            if (!$this->_canInvoiceItem($orderItem, $qtys)) {
                continue;
            }
            $item = $this->_convertor->itemToInvoiceItem($orderItem);
            if ($orderItem->isDummy()) {
                $qty = $orderItem->getQtyOrdered() ? $orderItem->getQtyOrdered() : 1;
            } else {
                if (isset($qtys[$orderItem->getId()])) {
                    $qty = (float) $qtys[$orderItem->getId()];
                } elseif (!count($qtys)) {
                    $qty = $orderItem->getQtyToInvoice();
                } else {
                    continue;
                }
            }
            $totalQty += $qty;
            $item->setQty($qty);
            //echo "price = ".$orderItem->getPrice()."<br/>";
            //die();
            // RON Begin
            $item->setPrice(Mage::helper('directory')->currencyConvert($orderItem->getPrice(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
            $orderItem->setTaxAmount(Mage::helper('directory')->currencyConvert($orderItem->getTaxAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
            $orderItem->setPrice(Mage::helper('directory')->currencyConvert($orderItem->getPrice(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
            $orderItem->setPriceInclTax(Mage::helper('directory')->currencyConvert($orderItem->getPriceInclTax(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
            $orderItem->setRowTotal(Mage::helper('directory')->currencyConvert($orderItem->getRowTotal(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
            $orderItem->setRowTotalInclTax(Mage::helper('directory')->currencyConvert($orderItem->getRowTotalInclTax(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
            $orderItem->setHiddenTaxAmount(Mage::helper('directory')->currencyConvert($orderItem->getHiddenTaxAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
            $orderItem->setWeeTaxAppliedAmmount(Mage::helper('directory')->currencyConvert($orderItem->getWeeTaxAppliedAmmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
            $orderItem->setWeeTaxAppliedRowAmmount(Mage::helper('directory')->currencyConvert($orderItem->getWeeTaxAppliedRowAmmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
            $orderItem->setWeeTaxDisposition(Mage::helper('directory')->currencyConvert($orderItem->getWeeTaxDisposition(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
            $orderItem->setWeeTaxRowDisposition(Mage::helper('directory')->currencyConvert($orderItem->getWeeTaxRowDisposition(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
            
            // RON End
            
            $invoice->addItem($item);
        }
        $invoice->setTotalQty($totalQty);
        $invoice->collectTotals();
        
        // RON Begin
        $invoice->setOrderCurrencyCode(self::newCurrencyCode);
        $invoice->setBaseToOrderRate(Mage::helper('directory')->currencyConvert('1',Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $invoice->setStoreToOrderRate(Mage::helper('directory')->currencyConvert('1',Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        
        $invoice->setShippingTaxAmount(Mage::helper('directory')->currencyConvert($invoice->getShippingTaxAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $invoice->setTaxAmount(Mage::helper('directory')->currencyConvert($invoice->getTaxAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $invoice->setGrandTotal(Mage::helper('directory')->currencyConvert($invoice->getGrandTotal(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $invoice->setShippingAmount(Mage::helper('directory')->currencyConvert($invoice->getShippingAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $invoice->setSubtotal(Mage::helper('directory')->currencyConvert($invoice->getSubtotal(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $invoice->setDiscountAmount(Mage::helper('directory')->currencyConvert($invoice->getDiscountAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $invoice->setHiddenTaxAmount(Mage::helper('directory')->currencyConvert($invoice->getHiddenTaxAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $invoice->setShippingHiddenTaxAmount(Mage::helper('directory')->currencyConvert($invoice->getShippingHiddenTaxAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        $invoice->setShippingInclTax(Mage::helper('directory')->currencyConvert($invoice->getShippingInclTax(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        // RON End
                
        $this->_order->getInvoiceCollection()->addItem($invoice);
        return $invoice;
    }

}

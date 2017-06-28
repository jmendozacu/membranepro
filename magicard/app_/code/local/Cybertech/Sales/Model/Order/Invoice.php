<?php
require_once 'Mage/Sales/Model/Order/Invoice.php';

/**
 * Overrides the register() method
 * to generate invoices in RON
 * from Magento 1.6.0
 */
class Cybertech_Sales_Model_Order_Invoice extends Mage_Sales_Model_Order_Invoice
{
	const newCurrencyCode = 'RON';

    /**
     * Register invoice
     *
     * Apply to order, order items etc.
     *
     * @return unknown
     */
    public function register()
    {
        if ($this->getId()) {
            Mage::throwException(Mage::helper('sales')->__('Cannot register existing invoice'));
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQty()>0) {
                $item->register();
            }
            else {
                $item->isDeleted(true);
            }
        }

        $order = $this->getOrder();
        $captureCase = $this->getRequestedCaptureCase();
        if ($this->canCapture()) {
            if ($captureCase) {
                if ($captureCase == self::CAPTURE_ONLINE) {
                    $this->capture();
                }
                elseif ($captureCase == self::CAPTURE_OFFLINE) {
                    $this->setCanVoidFlag(false);
                    $this->pay();
                }
            }
        } elseif(!$order->getPayment()->getMethodInstance()->isGateway() || $captureCase == self::CAPTURE_OFFLINE) {
            if (!$order->getPayment()->getIsTransactionPending()) {
                $this->setCanVoidFlag(false);
                $this->pay();
            }
        }

        $order->setTotalInvoiced($order->getTotalInvoiced() + $this->getGrandTotal());
        $order->setBaseTotalInvoiced($order->getBaseTotalInvoiced() + $this->getBaseGrandTotal());

        $order->setSubtotalInvoiced($order->getSubtotalInvoiced() + $this->getSubtotal());
        $order->setBaseSubtotalInvoiced($order->getBaseSubtotalInvoiced() + $this->getBaseSubtotal());

        $order->setTaxInvoiced($order->getTaxInvoiced() + $this->getTaxAmount());
        $order->setBaseTaxInvoiced($order->getBaseTaxInvoiced() + $this->getBaseTaxAmount());

        $order->setHiddenTaxInvoiced($order->getHiddenTaxInvoiced() + $this->getHiddenTaxAmount());
        $order->setBaseHiddenTaxInvoiced($order->getBaseHiddenTaxInvoiced() + $this->getBaseHiddenTaxAmount());

        $order->setShippingTaxInvoiced($order->getShippingTaxInvoiced() + $this->getShippingTaxAmount());
        $order->setBaseShippingTaxInvoiced($order->getBaseShippingTaxInvoiced() + $this->getBaseShippingTaxAmount());


        $order->setShippingInvoiced($order->getShippingInvoiced() + $this->getShippingAmount());
        $order->setBaseShippingInvoiced($order->getBaseShippingInvoiced() + $this->getBaseShippingAmount());

        $order->setDiscountInvoiced($order->getDiscountInvoiced() + $this->getDiscountAmount());
        $order->setBaseDiscountInvoiced($order->getBaseDiscountInvoiced() + $this->getBaseDiscountAmount());
        $order->setBaseTotalInvoicedCost($order->getBaseTotalInvoicedCost() + $this->getBaseCost());

        // RON Begin
		$order->setOrderCurrencyCode(self::newCurrencyCode);
		$order->setBaseToOrderRate(Mage::helper('directory')->currencyConvert('1',Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setStoreToOrderRate(Mage::helper('directory')->currencyConvert('1',Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        
		$order->setDiscountAmount(Mage::helper('directory')->currencyConvert($order->getDiscountAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setDiscountCanceled(Mage::helper('directory')->currencyConvert($order->getDiscountCanceled(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setDiscountInvoiced(Mage::helper('directory')->currencyConvert($order->getDiscountInvoiced(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setDiscountRefunded(Mage::helper('directory')->currencyConvert($order->getDiscountRefunded(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setGrandTotal(Mage::helper('directory')->currencyConvert($order->getGrandTotal(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setShippingAmount(Mage::helper('directory')->currencyConvert($order->getShippingAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setShippingCanceled(Mage::helper('directory')->currencyConvert($order->getShippingCanceled(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setShippingInvoiced(Mage::helper('directory')->currencyConvert($order->getShippingInvoiced(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setShippingRefunded(Mage::helper('directory')->currencyConvert($order->getShippingRefunded(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setShippingTaxAmount(Mage::helper('directory')->currencyConvert($order->getShippingTaxAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setShippingTaxRefunded(Mage::helper('directory')->currencyConvert($order->getShippingTaxRefunded(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setSubtotal(Mage::helper('directory')->currencyConvert($order->getSubtotal(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setSubtotalCanceled(Mage::helper('directory')->currencyConvert($order->getSubtotalCanceled(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		//$order->setSubtotalInvoiced(Mage::helper('directory')->currencyConvert($order->getSubtotalInvoiced(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setSubtotalRefunded(Mage::helper('directory')->currencyConvert($order->getSubtotalRefunded(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setTaxAmount(Mage::helper('directory')->currencyConvert($order->getTaxAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setTaxCanceled(Mage::helper('directory')->currencyConvert($order->getTaxCanceled(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		//$order->setTaxInvoiced(Mage::helper('directory')->currencyConvert($order->getTaxInvoiced(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setTaxRefunded(Mage::helper('directory')->currencyConvert($order->getTaxRefunded(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setTotalCanceled(Mage::helper('directory')->currencyConvert($order->getTotalCanceled(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		//$order->setTotalInvoiced(Mage::helper('directory')->currencyConvert($order->getTotalInvoiced(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setTotalOfflineRefunded(Mage::helper('directory')->currencyConvert($order->setTotalOfflineRefunded(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setTotalOnlineRefunded(Mage::helper('directory')->currencyConvert($order->setTotalOnlineRefunded(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		//$order->setTotalPaid(Mage::helper('directory')->currencyConvert($order->getTotalPaid(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setTotalRefunded(Mage::helper('directory')->currencyConvert($order->getTotalRefunded(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setAdjustmentNegative(Mage::helper('directory')->currencyConvert($order->getAdjustmentNegative(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setAdjustmentPositive(Mage::helper('directory')->currencyConvert($order->getAdjustmentPositive(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setSubtotalInclTax(Mage::helper('directory')->currencyConvert($order->getSubtotalInclTax(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setTotalDue(Mage::helper('directory')->currencyConvert($order->getTotalDue(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setHiddenTaxAmount(Mage::helper('directory')->currencyConvert($order->getHiddenTaxAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setShippingHiddenTaxAmount(Mage::helper('directory')->currencyConvert($order->getShippingHiddenTaxAmount(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setHiddenTaxInvoiced(Mage::helper('directory')->currencyConvert($order->getHiddenTaxInvoiced(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setHiddenTaxRefunded(Mage::helper('directory')->currencyConvert($order->getHiddenTaxRefunded(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
		$order->setShippingInclTax(Mage::helper('directory')->currencyConvert($order->getShippingInclTax(),Mage::app()->getBaseCurrencyCode(),self::newCurrencyCode));
        // RON End

        $state = $this->getState();
        if (is_null($state)) {
            $this->setState(self::STATE_OPEN);
        }

        Mage::dispatchEvent('sales_order_invoice_register', array($this->_eventObject=>$this, 'order' => $order));
        return $this;
    }

}

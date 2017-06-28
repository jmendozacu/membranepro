<?php

class Cybertech_Checkout_Block_Cart_Totals extends Mage_Checkout_Block_Cart_Totals
{
	public function getSubTotalInSpecifiedCurrency($currencyCode)
    {
        $totals = $this->getQuote()->getTotals();
		$subtotal = $totals["subtotal"]->getValue();
		
		$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
        $subtotalConverted = Mage::helper('directory')->currencyConvert($subtotal, $baseCurrencyCode, $currencyCode);
        $subtotalConverted = round($subtotalConverted,2);
		
        $html = $subtotalConverted;
        return $html;
    }
    
    
    
	public function getBaseGrandTotalInSpecifiedCurrency($currencyCode)
    {
        $grandTotal = $this->getQuote()->getBaseGrandTotal();
        
        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
        $totalConverted = Mage::helper('directory')->currencyConvert($grandTotal, $baseCurrencyCode, $currencyCode);
        $totalConverted = round($totalConverted,2);
		
        $html = $totalConverted;
        return $html;
    }
}
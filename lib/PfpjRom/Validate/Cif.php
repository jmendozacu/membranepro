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
 * @category   PfpjRom
 * @package    PfpjRom_CustomerRom
 * @copyright  Copyright (c) Daniel Ifrim
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

#require_once 'Zend/Validate/Abstract.php';

/**
 * Romanian CIF (Tax Identification Number | Cod de Identificare Fiscala) validator:
 *  - the input must have 13 digits;
 *  - format: can have as prefix 'RO' or 'RO ' if the fiscal entity pays TVA(VAT) tax.
 *  - format: ZZZZZZZZZC - ZZZZZZZZZ=code digits(max 9 digits in length) C=CRC number(0-9)
 *
 * Info about CIF:
 *
 * http://ro.wikipedia.org/wiki/Cod_fiscal
 * http://ro.wikipedia.org/wiki/Cod_de_identificare_fiscal%C4%83
 * description of algorithm: http://www.validari.ro/cif
 *
 * @see Zend_Validate_Abstract
 * @author     Daniel Ifrim
 */

class PfpjRom_Validate_Cif extends Zend_Validate_Abstract
{
    const CIF_INVALID = 'cifInvalid';
    const LENGTH_INVALID = 'cifLengthInvalid';
    const NUM_INVALID    = 'cifNumberInvalid';
    const CRC_INVALID    = 'cifCrcInvalid';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
    	self::CIF_INVALID => "CIF invalid.",
	    self::LENGTH_INVALID => "CIF '%value%' invalid, '%code%' should have between 1 and 10 digits.",
	    self::NUM_INVALID    => "CIF invalid, '%value%' should contain a number. For example RO1234567890 or 1234567890.",
	    self::CRC_INVALID    => "CIF invalid, '%crc%' is not a valid CRC."
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
    	'prefix'    => '_prefix',
	    'code'    	=> '_code',
	    'crc'    	=> '_crc'
    );

    /**
     * Code prefix.
     *
     * @var string
     */
    protected $_prefix;

    /**
     * Code number, has max 9 digits.
     *
     * @var string
     */
    protected $_code;

    /**
     * CRC
     *
     * @var string|null
     */
    protected $_crc;

    const CRC_TEST_KEY = "753217532";

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid CIF string.
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
	    $this->_setValue($value);

	    $isValid = true;

	    if (stripos($value, "RO") !== false) {
	    	$this->_prefix = "RO";
	    	$value = preg_replace('/RO\s*/i', '', $value);
	    }

	    if (strlen($value) > 10) {
			$this->_error(self::LENGTH_INVALID);
			return false;
		}

		if (!preg_match('/^([0-9]{1,9})([0-9])$/', $value, $matches)) {
			$this->_error(self::NUM_INVALID);
			return false;
		}

		list($this->_code, $this->_crc) = array_slice($matches, 1);

		if (!$this->checkCrc($value, $this->_crc)) {
			$this->_error(self::CRC_INVALID);
			$isValid = false;
		}

	    return $isValid;
    }

    protected function checkCrc($value, $crc)
    {
    	$isValid = false;
    	$s = 0;
    	$value = substr(strrev($value), 1);
    	$tk = strrev(self::CRC_TEST_KEY);
    	for ($i = 0; $i < strlen($value); $i++) {
    		$s += $value[$i] * $tk[$i];
    	}
    	$c = $s * 10 % 11;
    	if (!($c < 10))
    		$c = 0;

    	if ($crc == $c)
			$isValid = true;

		return $isValid;
    }
}
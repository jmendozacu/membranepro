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
 * Romanian CNP (Personal Identification Code | Cod Numeric Personal) validator:
 *  - the input must have 13 digits;
 *  - format: SYYMMDDRROOOC - S=sex(1-9) YY=year(00-99) MM=month(01-12) DD=day(01-31) OOO=ord number for the person(001-999) C=CRC number(0-9)
 *
 * Info about CNP:
 *
 * RO law: http://www.cdep.ro/pls/legis/legis_pck.htp_act_text?idt=4095
 * wiki: http://ro.wikipedia.org/wiki/Cod_numeric_personal
 * description of algorithm: http://www.validari.ro/cnp
 *
 * @see Zend_Validate_Abstract
 * @author     Daniel Ifrim
 */

class PfpjRom_Validate_Cnp extends Zend_Validate_Abstract
{
    const CNP_INVALID = 'cnpInvalid';
    const LENGTH_INVALID = 'cnpLengthInvalid';
    const NUM_INVALID    = 'cnpNumberInvalid';
    const SEX_INVALID    = 'cnpSexInvalid';
    //const YEAR_INVALID   = 'cnpYearInvalid';
    const MONTH_INVALID  = 'cnpMonthInvalid';
    const DAY_INVALID    = 'cnpDayInvalid';
    const DATE_INVALID   = 'cnpDateInvalid';
    const DATE_TOO_GREAT = 'cnpDateTooGreat';
    const REGION_INVALID = 'cnpRegionInvalid';
    const ORD_NB_INVALID = 'cnpOrdNbInvalid';
    const CRC_INVALID    = 'cnpCrcInvalid';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
    	self::CNP_INVALID => "CNP invalid.",
	    self::LENGTH_INVALID => "CNP '%value%' invalid, should have 13 characters in length.",
	    self::NUM_INVALID    => "CNP invalid, value should be a number.",
	    self::SEX_INVALID    => "CNP invalid, '%sex%' sex number given, should be 1-9.",
	    //self::YEAR_INVALID   => "CNP invalid, '%year%' is not an allowed year, should be 00-99.",
	    self::MONTH_INVALID  => "CNP invalid, '%month%' is not a month.",
	    self::DAY_INVALID    => "CNP invalid, '%day%' is not a day.",
	    self::DATE_INVALID   => "CNP invalid, '%date%' is not a valid date.",
	    self::DATE_TOO_GREAT => "CNP invalid, '%date%' is too great. A person can not be born in the future.",
	    self::REGION_INVALID => "CNP invalid, '%regionCode%' is not valid, should be 01-46, 51 or 52.",
	    self::ORD_NB_INVALID => "CNP invalid, '%ord%' is not valid person order number in the birth day.",
	    self::CRC_INVALID    => "CNP invalid, '%crc%' is not valid CRC."
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
	    'sex'    => '_sex',
	    'year'   => '_year',
	    'month'  => '_month',
	    'day'    => '_day',
	    'date'   => '_date',
	    'regionCode' => '_regionCode',
	    'ord'    => '_ord',
	    'crc'    => '_crc'
    );

    /**
     * Regions codes and names
     *
     * @var array
     */
    protected $_regionsCodes = array(
	    '01' =>	'Alba',
		'02' => 'Arad',
		'03' =>	'Argeş',
		'04' =>	'Bacău',
		'05' =>	'Bihor',
		'06' =>	'Bistriţa-Năsăud',
		'07' =>	'Botoşani',
		'08' =>	'Braşov',
		'09' =>	'Brăila',
		'10' =>	'Buzău',
		'11' =>	'Caraş-Severin',
		'12' =>	'Cluj',
		'13' =>	'Constanţa',
		'14' =>	'Covasna',
		'15' =>	'Dâmboviţa',
		'16' =>	'Dolj',
		'17' =>	'Galaţi',
		'18' =>	'Gorj',
		'19' =>	'Harghita',
		'20' =>	'Hunedoara',
		'21' =>	'Ialomiţa',
		'22' =>	'Iaşi',
		'23' =>	'Ilfov',
		'24' =>	'Maramureş',
		'25' =>	'Mehedinţi',
		'26' =>	'Mureş',
		'27' =>	'Neamţ',
		'28' =>	'Olt',
		'29' =>	'Prahova',
		'30' =>	'Satu Mare',
		'31' =>	'Sălaj',
		'32' =>	'Sibiu',
		'33' =>	'Suceava',
		'34' =>	'Teleorman',
		'35' =>	'Timiş',
		'36' =>	'Tulcea',
		'37' =>	'Vaslui',
		'38' =>	'Vâlcea',
		'39' =>	'Vrancea',
		'40' =>	'Bucureşti',
		'41' =>	'Bucureşti S.1',
		'42' =>	'Bucureşti S.2',
		'43' =>	'Bucureşti S.3',
		'44' =>	'Bucureşti S.4',
		'45' =>	'Bucureşti S.5',
		'46' =>	'Bucureşti S.6',
		'51' =>	'Călăraşi',
		'52' =>	'Giurgiu'
	);

    /**
     * Code for sex
     *
     * @var string
     */
    protected $_sex;

    /**
     * Year
     *
     * @var string|null
     */
    protected $_year;

    /**
     * Month
     *
     * @var string|null
     */
    protected $_month;

    /**
     * Day
     *
     * @var string|null
     */
    protected $_day;

    /**
     * Date YYYY-MM-DD
     *
     * @var string|null
     */
    protected $_date;

    /**
     * Region code
     *
     * @var string|null
     */
    protected $_regionCode;

    /**
     * Region name
     *
     * @var string|null
     */
    protected $_regionName;

    /**
     * Ord
     *
     * @var string|null
     */
    protected $_ord;

    /**
     * CRC
     *
     * @var string|null
     */
    protected $_crc;

    const CRC_TEST_KEY = "279146358279";

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid CNP string.
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
	    $this->_setValue($value);

	    $isValid = true;

		if (strlen($value) != 13) {
			$this->_error(self::LENGTH_INVALID);
			return false;
		}

		if (!preg_match('/^([0-9])([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{3})([0-9])$/', $value, $matches)) {
			$this->_error(self::NUM_INVALID);
			return false;
		}

		list($this->_sex, $this->_year, $this->_month, $this->_day, $this->_regionCode, $this->_ord, $this->_crc) = array_slice($matches, 1);

		if ($this->_sex <= 0) {
			$this->_error(self::NUM_INVALID);
			$isValid = false;
		}

		$validate_date = true;
		if ($this->_sex == 1 || $this->_sex == 2)
			$y_prefix = "19";
		elseif ($this->_sex == 3 || $this->_sex == 4)
			$y_prefix = "18";
		elseif ($this->_sex == 5 || $this->_sex == 6)
			$y_prefix = "20";
		elseif ($this->_sex == 7 || $this->_sex == 8 || $this->_sex == 9)
			$validate_date = false;

		/*if ($this->_year <= 0) {
			$this->_error(self::YEAR_INVALID);
			$isValid = false;
		}*/

		if ($this->_month <= 0 || $this->_month > 12) {
			$this->_error(self::MONTH_INVALID);
			$isValid = false;
		}

		if ($this->_day <= 0 || $this->_day > 31) {
			$this->_error(self::DAY_INVALID);
			$isValid = false;
		}

		$this->_date = null;
		if ($validate_date) {
			$this->_date = $y_prefix . $this->_year . '-' . $this->_month . '-' . $this->_day;
			if (!Zend_Validate::is($this->_date, 'Date')) {
				$this->_error(self::DATE_INVALID);
				$isValid = false;
			} else {
				$date = new Zend_Date($this->_date);
				if (!$date->isEarlier(date('Y-m-d'))) {
					$this->_error(self::DATE_TOO_GREAT);
					$isValid = false;
				}
			}
		}

		if (!isset($this->_regionsCodes[$this->_regionCode])) {
			$this->_error(self::REGION_INVALID);
			$isValid = false;
			$this->_regionName = null;
		} else {
			$this->_regionName = $this->_regionsCodes[$this->_regionCode];
		}

		if ($this->_ord <= 0) {
			$this->_error(self::ORD_INVALID);
			$isValid = false;
		}

		if (!$isValid)
			return false;

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
    	$tk = self::CRC_TEST_KEY;
    	for ($i = 0; $i < 12; $i++)
    		$s += $value[$i] * $tk[$i];
    	$c = $s % 11;
    	if (!($c < 10))
    		$c = 1;

		if ($crc == $c)
			$isValid = true;

		return $isValid;
    }
}
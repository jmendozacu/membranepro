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
 * @package    PfpjRom_CustomerRom
 * @copyright  Copyright (c) Daniel Ifrim
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Overrides address format renderer default
 *
 * @author     Daniel Ifrim
 */
class PfpjRom_CustomerRom_Block_Address_Renderer_Default extends Mage_Customer_Block_Address_Renderer_Default
{
    /**
     * Render address
     *
     * @param Mage_Customer_Model_Address_Abstract $address
     * @return string
     */
    public function render(Mage_Customer_Model_Address_Abstract $address, $format=null)
    {
        $address->getRegion();
        $address->getCountry();
        $address->explodeStreetAddress();


/* [start] PfpjRom edit */

        $formater = new PfpjRom_Filter_Template();

/* [end] PfpjRom edit */


        $data = $address->getData();
        if ($this->getType()->getHtmlEscape()) {
            foreach ($data as $key => $value) {
                if (is_object($value)) {
                    unset($data[$key]);
                } else {
                    $data[$key] = $this->htmlEscape($value);
                }
            }
        }

        /**
         * Remove data that mustn't show
         */
        if (!$this->helper('customer/address')->canShowConfig('prefix_show')) {
            unset($data['prefix']);
        }
        if (!$this->helper('customer/address')->canShowConfig('middlename_show')) {
            unset($data['middlename']);
        }
        if (!$this->helper('customer/address')->canShowConfig('suffix_show')) {
            unset($data['suffix']);
        }

        $formater->setVariables(array_merge($data, array('country'=>$address->getCountryModel()->getName())));

        $format = !is_null($format) ? $format : $this->getFormat($address);

        return $formater->filter($format);
    }
}
?>
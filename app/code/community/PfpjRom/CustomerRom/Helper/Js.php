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
 * CustomerRom JavaScript helper
 *
 * @author     Daniel Ifrim
 */

class PfpjRom_CustomerRom_Helper_Js extends Mage_Core_Helper_Js
{
    /**
     * Retrieve JS translator initialization javascript
     *
     * @return string
     */
    public function getTranslatorScript()
    {
        $script = 'if (Translator) { Translator.add('.$this->getTranslateJson().'); }';
        return $this->getScript($script);
    }

    /**
     * Retrieve JS translation array
     *
     * @return array
     */
    protected function _getTranslateData()
    {
        if ($this->_translateData ===null) {
        	foreach ($this->getConfig()->getFields() as $_fieldName => $_fieldConfig) {
	        	if ($this->getConfig()->isValidation($_fieldName)) {
	        		switch ($_fieldName) {
						case 'pfpj_cnp':
							if ($this->getConfig()->isValidation($_fieldName)) {
								$validCnp = new PfpjRom_Validate_Cnp();
								foreach ($validCnp->getMessageTemplates() as $messageId => $msgTpl) {
									$this->_translateData[$msgTpl] = __($msgTpl);
								}
							}
						break;
					}
	    		}
        	}
            foreach ($this->_translateData as $key=>$value) {
                if ($key == $value) {
                    unset($this->_translateData[$key]);
                }
            }
        }
        return $this->_translateData;
    }

    public function getConfig()
    {
    	return Mage::getModel("customerrom/address")->getConfig();
    }

}
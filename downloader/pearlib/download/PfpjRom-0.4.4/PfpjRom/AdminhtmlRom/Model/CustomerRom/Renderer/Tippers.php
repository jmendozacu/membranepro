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
 * Customer address tippers field renderer for admin
 *
 * @author     Daniel Ifrim
 */
class PfpjRom_AdminhtmlRom_Model_CustomerRom_Renderer_Tippers implements Varien_Data_Form_Element_Renderer_Interface
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<tr>'."\n";

        $value = $element->getValue();
        //$prefixId = $element->getForm()->getHtmlIdPrefix();

        /*$htmlAttributes = $element->getHtmlAttributes();
        foreach ($htmlAttributes as $key => $attribute) {
            if ('type' === $attribute) {
                unset($htmlAttributes[$key]);
                break;
            }
        }*/
        
        $elementClass = str_replace('required-entry', '', $element->getClass());
        if (trim($elementClass) == "")
        	$elementClass = trim($elementClass);
        $element->setClass($elementClass);
        
        $html.= '<td class="label">'.$element->getLabelHtml().'</td>'."\n";
        $html.= '<td class="value">'.$element->getElementHtml().'</td>'."\n";

        $html.= '</tr>'."\n";

        return $html;
    }

}
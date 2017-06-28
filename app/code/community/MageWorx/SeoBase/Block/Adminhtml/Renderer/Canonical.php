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
 * @category    Mage
 * @package     Mage_Weee
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml weee tax item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class MageWorx_SeoBase_Block_Adminhtml_Renderer_Canonical extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    public function _toHtml(){
        return '<tr>
        <td class="label"><label for="canonical_url_notice">Canonical URL</label></td>
        <td class="value">
            <input id="canonical_url_notice" class="required-entry input-text" type="text" readonly="1" style="border:10px" value="Switch to Store Scope for modify" name="product[canonical_url_notice]"></td>
        <td class="scope-label"><span class="nobr">[STORE VIEW]</span></td>
        </tr>';
    }
}

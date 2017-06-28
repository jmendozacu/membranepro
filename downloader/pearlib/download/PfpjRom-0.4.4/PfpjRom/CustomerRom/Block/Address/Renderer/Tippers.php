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
 * Tippers renderer for frontend.
 *  - implements radios
 *
 * @author     Daniel Ifrim
 */
class PfpjRom_CustomerRom_Block_Address_Renderer_Tippers
    extends Varien_Data_Form_Element_Radios
{
	protected $label_class = 'inline';
	protected $class = 'radio';
	
	public function __construct($attributes=array())
    {
    	if (!(isset($attributes['label_class']) && $attributes['label_class'] != '')) {
    		$attributes['label_class'] = $this->label_class;
    	}
        parent::__construct($attributes);
        $this->setType('radios');
    }
	
	/**
     * Retrieve Element HTML fragment
     *
     * @return string
     */
    public function getElementHtml()
    {
        if (!$this->getValue()) {
            $this->setData('disabled', 'disabled');
        }
        
        $html = '';
        $value = $this->getValue();
        if ($values = $this->getValues()) {
            foreach ($values as $option) {
                $html.= $this->_optionToHtml($option, $value);
            }
        }
        $html.= $this->getAfterElementHtml();
        return $html;
    }
    
    protected function _optionToHtml($option, $selected)
    {
        $html = '<input type="radio" name="'.$this->getName().'" '.$this->serialize(array('class', 'style', 'disabled', 'onchange', 'onclick', 'readonly'));
        if (is_array($option)) {
            $html.= ' value="'.$this->_escape($option['value']).'"  id="'.$this->getHtmlId().$option['value'].'"';
            if ($option['value'] == $selected) {
                $html.= ' checked="checked"';
            }
            $html.= ' />';
            $html.= '<label class="' . $this->getLabelClass() . '" for="'.$this->getHtmlId().$option['value'].'">'.$option['label'].'</label>';
        }
        elseif ($option instanceof Varien_Object) {
        	$html.= ' id="'.$this->getHtmlId().$option->getValue().'"'.$option->serialize(array('label', 'title', 'value', 'class', 'style'));
        	if (in_array($option->getValue(), $selected)) {
        	    $html.= ' checked="checked"';
        	}
        	$html.= ' />';
        	$html.= '<label class="' . $this->getLabelClass() . '" for="'.$this->getHtmlId().$option->getValue().'">'.$option->getLabel().'</label>';
        }
        $html.= $this->getSeparator() . "\n";
        return $html;
    }
    
    public function getAfterElementHtml()
    {
    	$html = '';
        $html .= parent::getAfterElementHtml();
    	
    	return $html;
    }
    
    public function addLabelClass($class)
    {
        $oldClass = $this->getLabelClass();
        $this->setLabelClass($oldClass.' '.$class);
        return $this;
    }
    
    public function getHtmlAttributes()
    {
    	return array_merge(parent::getHtmlAttributes(), array('label_class'));
    }
}

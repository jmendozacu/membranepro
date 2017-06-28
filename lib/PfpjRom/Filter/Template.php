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

/**
 * Overrides Template constructions filter.
 *  - adds condition in if construct pattern.
 *  - adds {inner_if condition}...{inner_else}...{/inner_if} construction
 *
 * TO DO: To implement some algorithm for parsing operands and operators.
 * 
 * @author     Daniel Ifrim
 */

class PfpjRom_Filter_Template extends Varien_Filter_Template
{
	const CONSTRUCTION_IF_CONDITION_OPERANDS = "/([^\s=><!]+)\s*((?:>=)|(?:<=)|\>|<|(?:===)|(?:!==)|(?:==)|(?:!=)){1}\s*(.*+)/si";
    /**
     * Cunstruction logic regular expression
     */
    const CONSTRUCTION_INNER_IF_PATTERN = '/{{inner_if\s*(.*?)}}(.*?)({{inner_else}}(.*?))?{{\\/inner_if\s*}}/si';
    const CONSTRUCTION_DEPEND_PATTERN = '/{{depend\s*(.*?)}}(.*?){{\\/depend\s*}}/si';
    const CONSTRUCTION_IF_PATTERN = '/{{if\s*(.*?)}}(.*?)({{else}}(.*?))?{{\\/if\s*}}/si';


    /**
     * Filter the string as template.
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
    	// "inner_if" operand should be first
    	foreach (array(
            self::CONSTRUCTION_INNER_IF_PATTERN => 'ifExtendedDirective',
            ) as $pattern => $directive) {
            if (preg_match_all($pattern, $value, $constructions, PREG_SET_ORDER)) {
                foreach($constructions as $index => $construction) {
                    $replacedValue = '';
                    $expression = null;
                    if($pattern == self::CONSTRUCTION_INNER_IF_PATTERN)
                    	if (!preg_match(self::CONSTRUCTION_IF_CONDITION_OPERANDS, $construction[1], $expression))
                    		continue;
                    $callback = array($this, $directive);
                    if(!is_callable($callback)) {
                        continue;
                    }
                    try {
                        $replacedValue = call_user_func($callback, $construction, $expression);
                    } catch (Exception $e) {
                        throw $e;
                    }
                    $value = str_replace($construction[0], $replacedValue, $value);
                }
            }
        }
    	
        // "depend" and "if" operands should be second
        foreach (array(
            self::CONSTRUCTION_DEPEND_PATTERN => 'dependDirective',
            self::CONSTRUCTION_IF_PATTERN     => 'ifDirective',
            ) as $pattern => $directive) {
            if (preg_match_all($pattern, $value, $constructions, PREG_SET_ORDER)) {
                foreach($constructions as $index => $construction) {
                    $replacedValue = '';
                    $expression = null;
                    if($pattern == self::CONSTRUCTION_IF_PATTERN)
                    	if (preg_match(self::CONSTRUCTION_IF_CONDITION_OPERANDS, $construction[1], $expression))
                    		$directive = "ifExtendedDirective";
                    $callback = array($this, $directive);
                    if(!is_callable($callback)) {
                        continue;
                    }
                    try {
                    	if($expression != null)
                        	$replacedValue = call_user_func($callback, $construction, $expression);
                        else
                        	$replacedValue = call_user_func($callback, $construction);
                    } catch (Exception $e) {
                        throw $e;
                    }
                    $value = str_replace($construction[0], $replacedValue, $value);
                }
            }
        }

        if(preg_match_all(self::CONSTRUCTION_PATTERN, $value, $constructions, PREG_SET_ORDER)) {
            foreach($constructions as $index=>$construction) {
                $replacedValue = '';
                $callback = array($this, $construction[1].'Directive');
                if(!is_callable($callback)) {
                    continue;
                }
                try {
					$replacedValue = call_user_func($callback, $construction);
                } catch (Exception $e) {
                	throw $e;
                }
                $value = str_replace($construction[0], $replacedValue, $value);
            }
        }
        return $value;
    }
    
    public function ifExtendedDirective($construction, $expression)
    {
        if (count($this->_templateVars) == 0) {
            return $construction[0];
        }
        
        $expression[3] = trim($expression[3], "'");
        $expression[3] = trim($expression[3], "\"");
        
        switch ($expression[2]) {
        	case '>=':
        		$condition = ($this->_getVariable($expression[1], '') >= $expression[3]);
        		break;
        	case '<=':
        		$condition = ($this->_getVariable($expression[1], '') <= $expression[3]);
        		break;
        	case '>':
        		$condition = ($this->_getVariable($expression[1], '') > $expression[3]);
        		break;
        	case '<':
        		$condition = ($this->_getVariable($expression[1], '') < $expression[3]);
        		break;
        	case '===':
        		$condition = ($this->_getVariable($expression[1], '') === $expression[3]);
        		break;
        	case '!==':
        		$condition = ($this->_getVariable($expression[1], '') !== $expression[3]);
        		break;
        	case '==':
        		$condition = ($this->_getVariable($expression[1], '') == $expression[3]);
        		break;
        	case '!=':
        		$condition = ($this->_getVariable($expression[1], '') != $expression[3]);
        		break;
        	default:
        		$condition = false;
        }

        if($condition) {
			return $construction[2];
        } else {
        	if (isset($construction[3]) && isset($construction[4]))
            	return $construction[4];
            else
            	return '';
        }
    }
}
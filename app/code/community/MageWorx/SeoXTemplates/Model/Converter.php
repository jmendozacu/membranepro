<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoXTemplates_Model_Converter extends Varien_Object
{
    protected $_item = null;

    abstract protected function __convert($vars, $templateCode);

    /**
     * Retrive converted string from template code
     * @param Mage_Catalog_Model_Abstract $item
     * @param string $templateCode
     * @return string
     */
//    public function convert(Mage_Catalog_Model_Abstract $item, $templateCode)
    public function convert($item, $templateCode)
    {
        $this->_setItem($item);
        $vars = $this->__parse($templateCode);
        $convertValue = $this->__convert($vars, $templateCode);

        return $convertValue;
    }

    /**
     *
     * @param Mage_Catalog_Model_Abstract $item
     */
    protected function _setItem($item)
    {
        $this->_item = $item;
    }

    /**
     * Retrive parsed vars from template code
     * @param string $templateCode
     * @return array
     */
    protected function __parse($templateCode)
    {
        $vars = array();
        preg_match_all('~(\[(.*?)\])~', $templateCode, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            preg_match('~^((?:(.*?)\{(.*?)\}(.*)|[^{}]*))$~', $match[2], $params);
            array_shift($params);

            if (count($params) == 1) {
                $vars[$match[1]]['prefix']     = $vars[$match[1]]['suffix']     = '';
                $vars[$match[1]]['attributes'] = explode('|', $params[0]);
            }
            else {
                $vars[$match[1]]['prefix']     = $params[1];
                $vars[$match[1]]['suffix']     = $params[3];
                $vars[$match[1]]['attributes'] = explode('|', $params[2]);
            }
        }
        return $vars;
    }

}

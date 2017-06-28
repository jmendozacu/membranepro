<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Helper_Function extends Mage_Core_Helper_Abstract
{
    /**
     * Recursive applies the callback to the elements of the given array
     *
     * @param string $func
     * @param array $array
     * @return array
     */
    public function arrayMapRecursive($func, $array)
    {
        if(!is_array($array)){
            $array = array();
        }

        foreach ($array as $key => $val) {
            if (is_array( $array[$key])) {
                $array[$key] = $this->arrayMapRecursive($func, $array[$key]);
            } else {
                $array[$key] = call_user_func($func, $val);
            }
        }
        return $array;
    }

    /**
     * Replace once occurrence of the search string with the replacement string
     *
     * @param string $search
     * @param string $replace
     * @param string $text
     * @return string
     */
    public function strReplaceOnce($search, $replace, $text)
    {
       $pos = mb_strpos($text, $search);
       return $pos !== false ? substr_replace($text, $replace, $pos, mb_strlen($search)) : $text;
    }
}
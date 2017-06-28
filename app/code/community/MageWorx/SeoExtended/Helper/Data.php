<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoExtended_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isEnterpriseSince113()
    {
        if (is_null($this->_enterpriseSince113)) {
            $mage = new Mage();
            if (is_callable(array($mage, 'getEdition')) && Mage::getEdition() == Mage::EDITION_ENTERPRISE
                && version_compare(Mage::getVersion(), '1.13.0.0', '>=')) {
                $this->_enterpriseSince113 = true;
            }
            else {
                $this->_enterpriseSince113 = false;
            }
        }
        return $this->_enterpriseSince113;
    }

    public function getCurrentFullActionName()
    {
        $controller = Mage::app()->getFrontController();
        if(is_object($controller) && is_callable(array($controller, 'getAction'))){
            $action = $controller->getAction();
            if(is_object($action) && is_callable(array($action, 'getFullActionName'))){
                $actionName = $action->getFullActionName();
                if($actionName) return $actionName;
            }
        }
        return null;
    }

    /**
     * Retrive specific filters data as array (use for canonical url)
     * @return array | false
     */
    public function getLayeredNavigationFiltersData()
    {
        $filterData     = array();
        $appliedFilters = Mage::getSingleton('catalog/layer')->getState()->getFilters();


        if (is_array($appliedFilters) && count($appliedFilters) > 0) {
            foreach ($appliedFilters as $item) {


                if (is_null($item->getFilter()->getData('attribute_model'))) {
                    //Ex: If $item->getFilter()->getRequestVar() == 'cat'
                    $use_in_canonical = 0;
                    $position         = 0;
                }
                else {
                    $use_in_canonical = $item->getFilter()->getAttributeModel()->getLayeredNavigationCanonical();
                    $position         = $item->getFilter()->getAttributeModel()->getPosition();
                }

                $filterData[] = array(
                    'name'             => $item->getName(),
                    'label'            => $item->getLabel(),
                    'code'             => $item->getFilter()->getRequestVar(),
                    'use_in_canonical' => $use_in_canonical,
                    'position'         => $position
                );
            }
        }
        return (count($filterData) > 0) ? $filterData : false;
    }
}
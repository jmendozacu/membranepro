<?php

class Magestore_Dailydeal_Block_Dailydeal extends Mage_Catalog_Block_Product_List

{
	public function _prepareLayout(){
		return parent::_prepareLayout();
	}

    /**
     * get collection dailydeal active
     *
     * @param 
     * @return Magestore_Dailydeal_Model_Dailydeal $dailydeals
     */
    public function getDailydeals(){
        $dailydeals=Mage::getModel('dailydeal/dailydeal')->getDailydeals();
        return $dailydeals;
    }

    /**
     * get dailydeal by product_id
     *
     * @param int $productId
     * @return Magestore_Dailydeal_Model_Dailydeal $dailydeal
     */
    public function getDailydealByProduct($productId){
        $dailydeal=Mage::getModel('dailydeal/dailydeal')->getDailydealByProduct($productId);
        return $dailydeal;
    }

    /**
     * get collection product from collection dailydeals active
     *
     * @param 
     * @return Magestore_Catalog_Model_Product $this->_productCollection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $productIds=array();
            $dailydeals=Mage::getModel('dailydeal/dailydeal')->getDailydeals();
            foreach ($dailydeals as $dailydeal) {
                if($dailydeal->getQuantity()>$dailydeal->getSold())
                $productIds[]=$dailydeal->getProductId();
            }

			
			$this->_productCollection = Mage::getResourceModel('catalog/product_collection')
											->setStoreId($this->getStoreId())
											->addFieldToFilter('entity_id',array('in'=>$productIds))
											->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
											->addMinimalPrice()
											->addTaxPercents()
											->addStoreFilter()
											;
								
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($this->_productCollection);						
        											
		}
        return $this->_productCollection;
    }
    public function getStoreId()
    {
        if ($this->hasData('store_id')) {
            return $this->_getData('store_id');
        }
        return Mage::app()->getStore()->getId();
    }
}
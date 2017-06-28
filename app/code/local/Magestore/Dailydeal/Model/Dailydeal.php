<?php

class Magestore_Dailydeal_Model_Dailydeal extends Mage_Core_Model_Abstract
{
	protected $_arrayfilter=null;
	protected $_dailydealCollection=null;
    public function _construct(){
        parent::_construct();
        $this->_init('dailydeal/dailydeal');
    }

    /**
     * get collection dailydeals active on current store
     *
     * @param 
     * @return Magestore_Dailydeal_Model_Dailydeal $dailydeals
     */
    public function getDailydeals(){
		if (is_null($this->_dailydealCollection)) {
        $store=Mage::app()->getStore()->getStoreId();
        $dailydeals=$this->getCollection()
                ->addFieldToFilter('status',3)
                ->addFieldToFilter('is_random',0)
                ->addFieldToFilter('store_id',$this->getArrayFilter($store))
                ->addFieldToFilter('product_id',array('nin'=>0));
				//->addFieldToFilter('close_time',array('nin'=>null));
		$this->_dailydealCollection=$dailydeals;
		}
        return $this->_dailydealCollection;
    }

    /**
     * get collection products by store
     *
     * @param  string $store example '1,2,3'
     * @return Magestore_Catalog_Model_Product $products
     */
    public function getLoadedProductCollection($store=null){
        
        $dailydeals=$this->getCollection()
            ->addFieldToFilter('status',3)
            ->addFieldToFilter('is_random',0)
            ->addFieldToFilter('product_id',array('nin'=>0));
        if ($store!=0)
            $dailydeals->addFieldToFilter('store_id',$this->getArrayFilter($store));
        $productIds=array();
        foreach ($dailydeals as $dailydeal) {
            if($dailydeal->getQuantity()>$dailydeal->getSold())
                $productIds[]=$dailydeal->getProductId();
        }
        $products=Mage::getModel('catalog/product')->getCollection()
                ->addFieldToFilter('entity_id',array('in'=>$productIds))
                ->addAttributeToSelect('*');
        return $products;
    }

    /**
     * get collection random products display on sidebar
     *
     * @param  
     * @return Magestore_Catalog_Model_Product $productsidebars
     */
    public function getSidebarProductCollection(){
        $current_product=Mage::registry('current_product');
		$productIds=$this->getDailydeals()->getAllProductIds();
		
		if ($current_product){
			if(array_search($current_product->getId(),$productIds)===false){
			}else{
				unset($productIds[array_search($current_product->getId(),$productIds)]);
			}
		}
        $productsidebar=array();
		
        $limit=Mage::getStoreConfig('dailydeal/sidebar/number_deal');
        if($limit > count($productIds))$limit = count($productIds);
        $rand_keys = array_rand($productIds, $limit);
        if ($limit==1){
            $productsidebar[]=$productIds[$rand_keys];
        }elseif ($limit>1) {
            foreach ($rand_keys as $rand_key) {
                $productsidebar[]=$productIds[$rand_key];
            }
        }
		
        $productsidebars=Mage::getModel('catalog/product')->getCollection()
                    ->addFieldToFilter('entity_id',array('in'=>$productsidebar))
                    ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes());
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($productsidebars);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($productsidebars);
        return $productsidebars;
    }

    /**
     * get dailydeal by product_id
     *
     * @param  int $productId
     * @return array $dailydeal
     */
    public function getDailydealByProduct($productId){
        $dailydeal=$this->getDailydeals()       
                ->addFieldToFilter('product_id',$productId);
		if($dailydeal->getSize())
            return $dailydeal->getFirstItem();
        return Mage::getModel('dailydeal/dailydeal');
    }


    /**
     * 
     *
     * @param  Magestore_Dailydeal_Model_Dailydeal $dailydeal
     * @return boolean example 'true'
     */
    public function isExitDealContainProduct($dailydeal) {
		if(!$dailydeal->getIsRandom())return false;
        $dailydeals=$this->getCollection()
            ->addFieldToFilter('status',3)
            ->addFieldToFilter('is_random',0)
            ->addFieldToFilter('product_id',$dailydeal->getProductId());
        if ($dailydeal->getStoreId()!=0)
            $dailydeals->addFieldToFilter('store_id',$this->getArrayFilter($dailydeal->getStoreId()));
        if ($dailydeals->getSize()>1) {
            return true;
        }else if ($dailydeals->getSize()==1){
            $dailydealcheck=$dailydeals->getFirstItem();
            if ($dailydealcheck->getId() !=$dailydeal->getId()) return true;
        } 
        return false;
    }

    /**
     * get limit of product on order
     *
     * @param  int $dailydealId
     * @return int $temp
     */
    public function getLimit($dailydealId){
        $quantity=$this->load($dailydealId)->getQuantity();
        $collection1 = Mage::getResourceModel('sales/order_collection')
                    ->addFieldToFilter('dailydeals',array('finset'=>$dailydealId));
        $temp=$quantity-$collection1->getSize();
        return $temp;
    }

    /**
     * get array to filter
     *
     * @param  string $store example '1,2,3'
     * @return array $array
     */
    public function getArrayFilter($store){
		if (is_null($this->_arrayfilter)) {
        $arr=explode(',',$store);
        $array=array();
        if($store!=0)
        foreach($arr as $a) {
            $array[]=array('finset'=>$a);
        }
        $array[]=array('finset'=>0);
		$this->_arrayfilter=$array;
		}
        return $this->_arrayfilter;
    }
}
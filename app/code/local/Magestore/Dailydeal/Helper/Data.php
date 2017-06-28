<?php

class Magestore_Dailydeal_Helper_Data extends Mage_Core_Helper_Abstract
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE    = 2;
    public function getOptionStatus()
    {
        return array(
                self::STATUS_ENABLE => Mage::helper('dailydeal')->__('Enable'),
                self::STATUS_DISABLE    => Mage::helper('dailydeal')->__('Disable'),                    
            );
    }

    public function getTablePrefix()
    {
        $tableName = Mage::getResourceModel('dailydeal/dailydeal')->getTable('dailydeal');  
        
        $prefix = str_replace('dailydeal','',$tableName);
        
        return $prefix;
    }

    /**
     * auto update status for dailydeals not disable
     *
     * @param 
     * @return 
     */
    public function updateDailydealStatus()
    {
		if(!Mage::registry('is_random_dailydeal'))
		Mage::register('is_random_dailydeal',1);
        $dailydeals = Mage::getModel('dailydeal/dailydeal')->getCollection()
                    ->addFieldToFilter('status',array('nin'=>array(2)));
        $store=Mage::app()->getStore()->getStoreId();
        if ($store!=0)        
            $dailydeals->addFieldToFilter('store_id',array(
                        array('finset'=>$store),
                        array('finset'=>0),
                    ));                
               
        try {
            $time_now=Mage::getModel('core/date')->timestamp(time());
            foreach($dailydeals as $dailydeal)
            {
				$oldstatus=$dailydeal->getStatus();
                $is_exit=Mage::getModel('dailydeal/dailydeal')->isExitDealContainProduct($dailydeal);
				try {
					$start_time=Mage::getModel('core/date')->timestamp(strtotime($dailydeal->getStartTime()));
                    $close_time=Mage::getModel('core/date')->timestamp(strtotime($dailydeal->getCloseTime()));
				} catch (Exception $e) {
					continue;
				}
			
                    if ($time_now < $start_time )$dailydeal->setStatus(1);
                    else if ($time_now > $close_time )$dailydeal->setStatus(4);
                    else if (!$is_exit||($dailydeal->getIsRandom()==1)) $dailydeal->setStatus(3);
                    $items=$dailydeal->getQuantity()- $dailydeal->getSold();
                    if(($dailydeal->getIsRandom()==1)&&($dailydeal->getStatus()==4))$dailydeal->setProductId(0);
                    if(($items<=0)&&($dailydeal->getIsRandom()==0))$dailydeal->setStatus(4);
					if($oldstatus!=$dailydeal->getStatus())
                    $dailydeal->save();
            }
            Mage::helper('dailydeal')->randomdeals();
        } catch(Exception $e) {
        
        }
    }

    /**
     * random product to generator dailydeal.
     *
     * @param Magestore_Dailydeal_Model_Dailydeal $randomdeal
     * @return 
     */
    public function randomDeal($randomdeal){
        if ($randomdeal->getProcess()==1) return;
        try {
                $time_left=Mage::getModel('core/date')->timestamp(strtotime($randomdeal->getTimeLeft()));
                $time_now=Mage::getModel('core/date')->timestamp(time());
            } catch (Exception $e) {
                $time_left=Mage::getModel('core/date')->timestamp(time());
                $time_now=Mage::getModel('core/date')->timestamp(time());
            }
            if($time_left<=$time_now)
            {
				$randomdeal->setProcess(1)->save();
                $randomdeal->setProductId(0);
                $productIds=explode(",",$randomdeal->getProducts());
                $productdeals=$randomdeal->getProductsDeal();
                $array_temp=explode(",",$productdeals);
                if(count($array_temp)==count($productIds))
                    $productdeals=NULL;
                $array_temp=explode(",",$productdeals);

                $productDealIds=Mage::getModel('dailydeal/dailydeal')->getLoadedProductCollection($randomdeal->getStoreId())->getAllIds();
                $arr_random=array_diff(array_diff($productIds,$productDealIds),$array_temp);
                if (count($arr_random)==0) {
                    $randomdeal->setProcess(0)->save();
                    return;
                }
                {
                    $rand_keys = array_rand($arr_random, 1);
                    { 
                      if ($productdeals==NULL){
                            $randomdeal->setProductsDeal($arr_random[$rand_keys]);
                        }  else {
                            $randomdeal->setProductsDeal($productdeals.','.$arr_random[$rand_keys]);
                        }

                        $deal_time=$randomdeal->getDealTime();
                        $start_time=Mage::getModel('core/date')->timestamp(strtotime($randomdeal->getStartTime()));
                        $n=ceil(($time_now-$start_time)/(3600*$deal_time));
                        $date = new DateTime($randomdeal->getStartTime());
                        $date->modify($deal_time*$n.' hours');
                        $randomdeal->setTimeLeft($date->format('Y-m-d H:i:s'));
						if (!$randomdeal->getTimeLeft()) {
							$randomdeal->setProcess(0)->save();
						return;
						}
                        $randomdeal->setSold(0);

                        $save=$this->random($randomdeal->getSave());
                        $quantity=$this->random($randomdeal->getQuantity());

                        $date->modify(-$deal_time.' hours');
                        $product=Mage::getModel('catalog/product')->load($arr_random[$rand_keys]);
                        $deal_price=$product->getPrice()-$save*$product->getPrice()/100;
                        $title=$this->getDailydealTitle($randomdeal->getTitle(),$product->getName(),$save);

                        $deal=Mage::getModel('dailydeal/dailydeal')
                        ->setTitle($title)
                        ->setProductId($product->getId())
                        ->setProductName($product->getName())
                        ->setThumbnailImage($randomdeal->getThumbnailImage())
                        ->setSave($save)
                        ->setDealPrice($deal_price)
                        ->setQuantity($quantity)
                        ->setSold(0)
                        ->setStartTime($date->format('Y-m-d H:i:s'))
                        ->setCloseTime($randomdeal->getTimeLeft())
                        ->setIsRandom(0)
                        ->setStatus(1)
                        ->setStoreId($randomdeal->getStoreId());
						if (!$deal->getProductId()) {
							$randomdeal->setProcess(0)->save();
						return;
						}
                        $deal->save();
                        $is_exit=Mage::getModel('dailydeal/dailydeal')->isExitDealContainProduct($deal);
                        if(!$is_exit) $deal->setStatus(3)->save();
                        $randomdeal->setProductId($deal->getId());
                        $randomdeal->save();
                    }
                }    
			$randomdeal->setProcess(0)->save();
            }
        return;
    }

    /**
     * auto generator dailydeal
     *
     * @param  
     * @return 
     */
    public function randomdeals(){
        /** fix random */
		$check = Mage::getModel('dailydeal/dailydeal')->getCollection()
                    ->addFieldToFilter('status',array('in'=>array(3)))
                    ->addFieldToFilter('is_random',array('in'=>array(1)));
        $store=Mage::app()->getStore()->getStoreId();
        if ($store!=0)        
            $check->addFieldToFilter('store_id',array(
                        array('finset'=>$store),
                        array('finset'=>0),
                    ));
		$check->addFieldToFilter('process',0);
		if(!$check->getSize()){
			Mage::getModel('core/session')->setData('fix_randomdeal', 1);
		}
		/** end fix **/
        $dailydeals = Mage::getModel('dailydeal/dailydeal')->getCollection()
                    ->addFieldToFilter('status',array('in'=>array(3)))
                    ->addFieldToFilter('is_random',array('in'=>array(1)));
        $store=Mage::app()->getStore()->getStoreId();
        if ($store!=0)        
            $dailydeals->addFieldToFilter('store_id',array(
                        array('finset'=>$store),
                        array('finset'=>0),
                    ));
		if(Mage::getModel('core/session')->getData('fix_randomdeal') == 1){
			foreach($dailydeals as $dailydeal){
				if($dailydeal->getProcess(0))
				$dailydeal->setProcess(0)->save();
			}
			Mage::getModel('core/session')->setData('fix_randomdeal', 0);
		}
        foreach($dailydeals as $dailydeal){
            $this->randomDeal($dailydeal);
        }
        return;
    }
        
    public function isDisplayOnSidebar() {
        $temp=Mage::getStoreConfig('dailydeal/sidebar/is_display');
        return $temp;
    }
    public function displayOnLeftRightSideBar() {
        $temp=Mage::getStoreConfig('dailydeal/sidebar/display_on_left_right_sidebar');
        return $temp;
    }
    public function getDailydealUrl()
    {
        $url = $this->_getUrl("dailydeal", array());
        return $url;            
    }
    public function getDailydealTitle($title,$product_name,$save){
        return str_replace(
            array(
                '{{product_name}}', 
                '{{save}}'
            ),
            array(
                $product_name,
                $save.'%'
            ),
            $title
        );
    } 

    /**
     * random value.
     *
     * @param string $value
     * @return int $random
     */
    public function random($value){
        try{
            if (strpos($value,'-'))
            {
                $values=explode('-',$value);
                $arr = range($values[0],$values[1]);
            }else{
                $arr=explode(';', $value);
            }
            $random=$arr[array_rand($arr,1)];
        }catch (Exception $e) {}  
        return $random;
    }    
}
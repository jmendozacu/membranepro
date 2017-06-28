<?php

ini_set('max_execution_time', 3600);
	
require_once '../app/Mage.php';
umask(0);
Mage::app('default');
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
error_reporting(E_All);

try {
    $currentModel =  Mage::getModel('sales/order');
	//echo 'order data: '; Zend_Debug::dump($currentModel);
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

$ordersToImport = array(
					// '20025',
					// '20026',
					// '20027',
					// '20028',
					// '20029',
					// '20030',
					// '20031',
					// '20032',
					// '20033',
					// '20034',
					// '20035',
					// '20036',
					// '20037',
					// '20038',
					// 'CPC-13-0435',
					// 'CPC-13-0436',
					// 'CPC-13-0437',
					// 'CPC-13-0438',
					// 'CPC-13-0439',
					// 'CPC-13-0440',
					// 'CPC-13-0441',
					// 'CPC-13-0442',
					// 'CPC-13-0443',
					// 'CPC-13-0444',
					// 'CPC-13-0445',
					// 'CPC-13-0446',
					// 'CPC-13-0447',
					// 'CPC-13-0448',
					// 'CPC-13-0449',
					// 'CPC-13-0450',
					// 'CPC-13-0450-1',
					// 'CPC-13-0451',
					// 'CPC-13-0452',
					// 'CPC-13-0453',
					// 'CPC-13-0454-1',
					// 'CPC-13-0455-1',
					// 'CPC-13-0456',
					// 'CPC-13-0457',
					// 'CPC-13-0458',
					// 'CPC-13-0459-1',
					// 'CPC-13-0460',
					// 'CPC-13-0461',
					// 'CPC-13-0462-1',
					// 'CPC-13-0463',
					// 'CPC-13-0464',
					// 'CPC-13-0465',
					// 'CPC-13-0466',
					// 'CPC-13-0467-1',
					// 'CPC-13-0468-1',
					// 'CPC-13-0469-1',
					// 'CPC-13-0470',
					// 'CPC-13-0471',
					// 'CPC-13-0472',
					// 'CPC-13-0473-1',
					// 'CPC-13-0474',
					// 'CPC-13-0474-1',
					// 'CPC-13-0475',
					// 'CPC-13-0476',
					// 'CPC-13-0477',
					// 'CPC-13-0477-1',
					// 'CPC-13-0478',
					// 'CPC-13-0479',
					// 'CPC-13-0480',
					// 'CPC-13-0481',
					// 'CPC-13-0482-1',
					// 'CPC-13-0483',
					// 'CPC-13-0484-1',
					// 'CPC-13-0485',
					// 'CPC-13-0486',
					// 'CPC-13-0487',
					// 'CPC-13-0488',
					// 'CPC-13-0489',
					// 'CPC-13-0490',
					// 'CPC-13-0491',
					// 'CPC-13-0492',
					// 'CPC-13-0493',
					// 'CPC-13-0494',
					// 'CPC-13-0495',
					// 'CPC-13-0496',
					// 'CPC-13-0497',
					// 'CPC-13-0499',
					// 'CPC-13-0500',
				);
				
rsort($ordersToImport);

if (count($ordersToImport)) {
	foreach ($ordersToImport as $orderIncrementId) {
		$output = '';
		
		try { // load ext order model
		    $extOrders = Mage::getModel('externaldbconnection/orders')->load($orderIncrementId, 'increment_id');
		} catch (Exception $e) {
		    echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	
		if($extOrders->getId()) {
			$output .= '<h4>External order ' . $orderIncrementId . ' was loaded.</h4>';
			/*
			 * load customer data
			 * !!! be sure that the ext customers has already been imported and exists !!!
			 * !!! otherwise the order will not be imported !!!
			*/
			$customerData = Mage::getModel('customer/customer')->loadByEmail($extOrders->getCustomerEmail());
			//echo 'customer data'; Zend_Debug::dump($customerData->getData()); die();
			if ($customerData->getId()) {
				/*
				 * start creating new order
				*/
				$output .= '<p>';
				
				$storeId = $extOrders->getStoreId();
				$reservedOrderId = Mage::getSingleton('eav/config')->getEntityType('order')->fetchNewIncrementId($storeId);
				/*
				 * now we are ready to build the new order
				 * we want to keep original increment id so we have to set it here
				*/
				$transaction = Mage::getModel('core/resource_transaction');
				$newOrder =  Mage::getModel('sales/order');
				$newOrder->setIncrementId($orderIncrementId);
				
				$newOrder->setStoreId($storeId)
						 ->setQuoteId(0)
						 ->setGlobalCurrencyCode($extOrders->getGlobalCurrencyCode())
						 ->setBaseCurrencyCode($extOrders->getBaseCurrencyCode())
						 ->setStoreCurrencyCode($extOrders->getStoreCurrencyCode())
						 ->setOrderCurrencyCode($extOrders->getOrderCurrencyCode())
						 ->setCustomerEmail($customerData->getEmail())
						 ->setCustomerId($customerData->getId())
						 ->setCustomerFirstname($customerData->getFirstname())
						 ->setCustomerLastname($customerData->getLastname())
						 ->setCustomerGroupId($customerData->getGroupId())
						 ->setCustomerIsGuest($extOrders->getCustomerIsGuest())
						 ->setEmailSent($extOrders->getEmailSent())
						 ->setRemoteIp($extOrders->getRemoteIp());
					
				$output .= 'New order: ' . $orderIncrementId . ' (ext ID = ' . $extOrders->getId() . ')';
				//Zend_Debug::dump($newOrder->getData()); die();
				
				/* 
				 * get ordered items
				 * we could have more than one item ordered
				 * so we have to get the collection of items and iterate through results
				*/
				$extItems = Mage::getModel('externaldbconnection/item')
					->getCollection()
					->addFieldToFilter('order_id', array('eq' => $extOrders->getId()));
					
				$prodType = array();
				$subTotal = 0;
				
				foreach($extItems as $item) {
					// array containing all product types
					$prodType[$item->getProductType()] = true;
					$orderItem = Mage::getModel('sales/order_item');
					
					foreach ($item->getData() as $key => $val) {
						if ($key != 'item_id' && $key != 'order_id' && $key != 'parent_item_id' && $key != 'quote_item_id') {
							$orderItem->setData($key, $val);
						}
					}
	 				
					// $orderItem = Mage::getModel('sales/order_item')
						// ->setStoreId($storeId)
						// ->setQuoteParentItemId(NULL)
						// ->setProductId($item->getProductId())
						// ->setProductType($item->getProductType())
						// ->setQtyBackordered(NULL)
						// ->setTotalQtyOrdered($item->getTotalQtyOrdered())
						// ->setQtyOrdered($item->getQtyOrdered())
						// ->setQtyCanceled($item->getQtyCanceled())
						// ->setName($item->getName())
						// ->setSku($item->getSku())
						// ->setPrice($item->getPrice())
						// ->setBasePrice($item->getBasePrice())
						// ->setOriginalPrice($item->getOriginalPrice())
						// ->setTaxAmount($item->getTaxAmount())
						// ->setBaseTaxAmount($item->getBaseTaxAmount())
						// ->setTaxPercent($item->getTaxPercent())
						// ->setBaseTaxPercent($item->getTaxPercent())
						// ->setDiscountAmount($item->getDiscountAmount())
						// ->setRowTotal($item->getRowTotal())
						// ->setBaseRowTotal($item->getBaseRowTotal())
						// ->setRowTotalInclTax($item->getRowTotalInclTax())
						// ->setBaseRowTotalInclTax($item->getBaseRowTotalInclTax());
						
					$subTotal += $item->getRowTotal();
					$newOrder->addItem($orderItem);
					$itemData = true;
				}
	
				/*
				 * try to find if shipping address is required
				*/
				if(count($prodType) == 1 && $prodType['downloadable'] === true) {
					/*
					 * all product type are downloadable
					 * so, shipping is not required, we can set it as true
					*/
					$shippingAddress = Mage::getModel('sales/order_address');
					$newOrder->setShippingAddress($shippingAddress);
					$shippingData = true;
				}
				
				$baseSubtotal = $subTotal + $extOrders->getBaseShippingAmount() + $extOrders->getBaseTaxAamount();
				$newOrder ->setSubtotal($subTotal)
						  ->setShippingAmount($extOrders->getBaseShippingAmount())
						  ->setTaxAmount($extOrders->getTaxAmount())
						  ->setDiscountAmount()
						  ->setTotalPaid($extOrders->getTotalPaid())
						  ->setBaseSubtotal($extOrders->getBaseSubtotal())
						  ->setGrandTotal($extOrders->getGrandTotal())
						  ->setBaseGrandTotal($extOrders->getGrandTotal())
						  ->setEmailSent($extOrders->getEmailSent())
						  ->setDiscountAmount($extOrders->getDiscountAmount());  
						  
				//$transaction->addObject($newOrder);
				$output .= ', item(s) set';
				
				// get ext customer addresses
				$extAddress = Mage::getModel('externaldbconnection/address')
						->getCollection()
						->addFieldToFilter('parent_id', $extOrders->getId());
				
				foreach($extAddress as $extAddress) {
					//Zend_Debug::dump($extAddress->getData());
					
					if($extAddress->getAddressType() == 'billing') {
						// set billing address (required)
						$billingData = false;
						$billingAddress = Mage::getModel('sales/order_address')
							->setStoreId($storeId)
							->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
							->setCustomerId($customerData->getId())
							->setCustomerAddressId($extAddress->getCustomerAddressId())
							->setPrefix($extAddress->getPrefix())
							->setFirstname($extAddress->getFirstname())
							->setMiddlename($extAddress->getMiddlename())
							->setLastname($extAddress->getLastname())
							->setSuffix($extAddress->getSuffix())
							->setCompany($extAddress->getCompany())
							->setStreet($extAddress->getStreet())
							->setCity($extAddress->getCity())
							->setCountryId($extAddress->getCountryId())
							->setRegion($extAddress->getRegion())
							->setRegionId($extAddress->getRegionId())
							->setPostcode($extAddress->getPostcode())
							->setTelephone($extAddress->getTelephone())
							->setFax($extAddress->getFax());
							
						$newOrder->setBillingAddress($billingAddress);
						$billingData = true; 
						$output .= ', billing set';
					} elseif($extAddress->getAddressType() == 'shipping') {
						// set shipping address 
						$shippingData = false;
						$shippingAddress = Mage::getModel('sales/order_address')
							->setStoreId($storeId)
							->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
							->setCustomerId($customerData->getId())
							->setCustomerAddressId($extAddress->getCustomerAddressId())
							->setPrefix($extAddress->getPrefix())
							->setFirstname($extAddress->getFirstname())
							->setMiddlename($extAddress->getMiddlename())
							->setLastname($extAddress->getLastname())
							->setSuffix($extAddress->getSuffix())
							->setCompany($extAddress->getCompany())
							->setStreet($extAddress->getStreet())
							->setCity($extAddress->getCity())
							->setCountryId($extAddress->getCountryId())
							->setRegion($extAddress->getRegion())
							->setRegionId($extAddress->getRegionId())
							->setPostcode($extAddress->getPostcode())
							->setTelephone($extAddress->getTelephone())
							->setFax($extAddress->getFax());
							
						$methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
						$newOrder->setShippingAddress($shippingAddress)
							  ->setShippingMethod($extOrders->getShippingMethod())
							  ->setShippingDescription($extOrders->getShippingDescription())
							  ->setBaseShippingAmount($extOrders->getBaseShippingAmount());
							  // some error i am getting here need to solve further
							  
						$shippingData = true;
						$output .= ', shipping set';
					}
				}
	
				// get ext payment methodes
				$extPayment = Mage::getModel('externaldbconnection/payment')->load($extOrders->getId(), 'parent_id');
				//Zend_Debug::dump($extPayment->getData()); die();
				
				$paymentMethode = Mage::getModel('sales/order_payment')
					// we have to set all of the fields
					->setStoreId($storeId)
					->setCustomerPaymentId(0)
					->setMethod($extPayment->getMethod())
					->setPoNumber($extPayment->getPoNumber())
					->setCcExpMonth($extPayment->getCcExpYear())
					->setCcExpYear($extPayment->getCcExpMonth())
					->setCcLast4($extPayment->getCcLast4())
					->setCcOwner($extPayment->getCcOwner())
					->setCcType($extPayment->getCcType())
					->setCcNumberEnc($extPayment->getCcNumberEnc());
					
				$newOrder->setPayment($paymentMethode);
				$output .= ', payment set';
	
				if ($billingData && $shippingData && $itemData) {
					$canSave = true;
				} else {
					$reason = '';
					!$itemData ? $reason .=' missing item data' : '';
					!$billingData ? $reason .=' missing billig data' : '';
					!$shippingData ? $reason .=' missing shipping data' : '';
					$output .= $orderIncrementId . ' (ext ID = ' . $extOrders->getId() . ') could not be imported. Reason: ' . $reason;
					continue;
				}
				
				if($canSave) {
					$errorOnSave = false;
					$output .= ' => can save.';
					
					$transaction->addObject($newOrder);
					// $transaction->addCommitCallback(array($newOrder, 'place'));
					// $transaction->addCommitCallback(array($newOrder, 'save'));
					
					try
					{
						$transaction->save();
						$output .= ' Transaction saved.';
					}
					catch(Exception $e)
					{
						echo '<p>Exception = ' . $e->getMessage() . '</p>';
						$errorOnSave = true;
					}
					
					
					if (!$errorOnSave) {
						// reload the order to change state, status, hystory and created date
						$importedOrder = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
						$importedOrderId = $importedOrder->getId();
						
						$orderState = $extOrders->getState();
						$orderPaid = $extOrders->getBaseTotalPaid();
						
						// Zend_Debug::dump($importedOrder); die();
						
						switch($orderState){
							case 'canceled':
								//$importedOrder->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
								$importedOrder->setData('state', "canceled");
							    $importedOrder->setStatus($extOrders->getStatus());
							    $history = $importedOrder->addStatusHistoryComment('Automatically marked as CANCELED by batch import on ' . date("F j, Y"), false);
							    $history->setIsCustomerNotified(false);
								break;
							case 'complete';
								//$importedOrder->setState(Mage_Sales_Model_Order::STATE_COMPLETE, true);
								$importedOrder->setData('state', "complete");
							    $importedOrder->setStatus($extOrders->getStatus());
							    $history = $importedOrder->addStatusHistoryComment('Automatically marked as COMPLETE by batch import on ' . date("F j, Y"), false);
							    $history->setIsCustomerNotified(false);
								break;
							case 'holded ';
								//$importedOrder->setState(Mage_Sales_Model_Order::STATE_HOLDED, true);
								$importedOrder->setData('state', "holded");
							    $importedOrder->setStatus($extOrders->getStatus());
							    $history = $importedOrder->addStatusHistoryComment('Automatically marked as HOLDED by batch import on ' . date("F j, Y"), false);
							    $history->setIsCustomerNotified(false);
								break;
							case 'new';
								//$importedOrder->setState(Mage_Sales_Model_Order::STATE_NEW, true);
								$importedOrder->setData('state', "new");
							    $importedOrder->setStatus($extOrders->getStatus());
							    $history = $importedOrder->addStatusHistoryComment('Automatically marked as NEW by batch import on ' . date("F j, Y"), false);
							    $history->setIsCustomerNotified(false);
								break;
							case 'pending_payment';
								//$importedOrder->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true);
								$importedOrder->setData('state', "pending_payment");
							    $importedOrder->setStatus($extOrders->getStatus());
							    $history = $importedOrder->addStatusHistoryComment('Automatically marked as PENDING PAYMENT by batch import on ' . date("F j, Y"), false);
							    $history->setIsCustomerNotified(false);
								break;
							case 'processing';
								//$importedOrder->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
								$importedOrder->setData('state', "processing");
							    $importedOrder->setStatus($extOrders->getStatus());
							    $history = $importedOrder->addStatusHistoryComment('Automatically marked as PROSSECING by batch import on ' . date("F j, Y"), false);
							    $history->setIsCustomerNotified(false);
								break;
						}
						
						//Zend_Debug::dump($importedOrder->getData()); die();
						
						$importedOrder->setCreatedAt($extOrders->getCreatedAt());
						$importedOrder->setBaseTotalPaid($orderPaid);
						$importedOrder->save();
						$canSave = false;
						
						$output .= '</p>';
						$output .= '<p>Updated: state, status, hystory and changed created date.</p>';
					}
					
	
				} else {
					$output .= '<p>Order ' . $orderIncrementId . ' cannot be saved. An error occurred.</p>';
				} // end if $canSave
	
			} else {
				$output = '<p>Customer does not exist for order ' . $orderIncrementId . ' (ext ID = ' . $extOrders->getId() . ')</p>';
			}  // end if $customerData()
		} else {
			$output = '<h4>External order ' . $orderIncrementId . ' does not exist.</h4>';
		} // end if $extOrders
		
		echo $output;
		Mage::log(strip_tags($output), false, 'imported_orders.log');
	}

	// if (is_int($orderIncrementId)) {
		// $lastIncrementId = (int)$orderIncrementId - 1;
	// } else {
		// /*
		 // * find the last integer part of the increment id
		 // * specific for increment id like CPC-13-0435
		// */
		// $orderIncrementId = explode('-', $orderIncrementId);
		// $incrementNumber = end($orderIncrementId) - 1;
		// // rebuild increment id
		// $lastIncrementId = '';
		// for ($i = 0; $i < count($orderIncrementId); $i++) {
			// $lastIncrementId .= $orderIncrementId[$i] . '-';
		// }
	// 	
		// $lastIncrementId .= $incrementNumber;
	// }
	
	$lastIncrementId .= $orderIncrementId;
	echo '<p>LSTID = ' . $orderIncrementId . '</p>';
	
	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
	
	/*
	 * we have to update last increment id
	 * to be able to place new orders from frontend
	 * after the import process was finished
	*/
	$tablePrefix = Mage::getConfig()->getTablePrefix();
	$query = "UPDATE " . $tablePrefix . "eav_entity_store
			  INNER JOIN " . $tablePrefix . "eav_entity_type 
			  ON " . $tablePrefix . "eav_entity_type.entity_type_id = " . $tablePrefix . "eav_entity_store.entity_type_id
			  SET " . $tablePrefix . "eav_entity_store.increment_last_id='" . $lastIncrementId . "'
			  WHERE " . $tablePrefix . "eav_entity_type.entity_type_code='order' AND " . $tablePrefix . "eav_entity_store.store_id = '".$storeId."'";
	//echo $query;
	$write->query($query);
	
} else {
	echo 'There are no orders to import!';
	Mage::log('There are no orders to import!', false, 'imported_orders.log');
}

?>
<?php

class Redstage_OrderFollowupEmail_Model_Cron {

	const XML_PATH_TEMPLATE						= 'orderfollowupemail/settings/template';
	const XML_PATH_REMINDER_PERIOD				= 'orderfollowupemail/settings/reminder_period';
	const XML_PATH_EMAIL_IDENTITY				= 'orderfollowupemail/settings/identity';
	const XML_PATH_EMAIL_COPY_TO				= 'orderfollowupemail/settings/email_copy_to';
	const XML_PATH_EMAIL_OVERRIDE				= 'orderfollowupemail/settings/email_override';

	public function processFollowupEmails( $observer ){ // The checker

		// Load helper
		$helper = Mage::helper('orderfollowupemail');
		$helper->log( 'Running Cron: '. Mage::getModel('core/date')->date() );

		// Load enabled
		$enabled = Mage::getStoreConfig('orderfollowupemail/settings/enabled');
		if( !$enabled ){
			$helper->log( 'ERROR - Module not enabled' );
			return false;
		}

		// Load template ID
		$email_template_id = Mage::getStoreConfig( self::XML_PATH_TEMPLATE );
		if( !$email_template_id ){
			$helper->log( 'ERROR - Email Template NOT FOUND' );
			return false;
		}

		// Load reminder period
		$reminder_period = Mage::getStoreConfig( self::XML_PATH_REMINDER_PERIOD );
		if( !$reminder_period ){
			$helper->log( 'ERROR - Reminder Period NOT FOUND' );
			return false;
		}

		// Load unprocessed orders
		$unprocessed_orders = Mage::getResourceModel('sales/order_collection')
			->addAttributeToFilter( 'status', 'complete' );

		// Filter out the orders that we have already processed -- we don't want to be annoying
		$tableName = Mage::getSingleton('core/resource')->getTableName('orderfollowupemail_log');
		$unprocessed_orders->getSelect()->joinLeft( $tableName, $tableName .".order_id=main_table.entity_id", array() );
		$unprocessed_orders->getSelect()->where( ' '. $tableName .'.order_id IS NULL ' );

		//$helper->log( $unprocessed_orders->getSelect() );

		// For every unprocessed order...
		foreach( $unprocessed_orders as $unprocessed_order ){

			$unprocessed_order = Mage::getModel('sales/order')->load( $unprocessed_order->getId() );

			// If order is not old enough yet, skip
			if(
				( strtotime( $unprocessed_order->getUpdatedAt() ) + ( $reminder_period * 3600 ) ) // order last updated + grace period in seconds
				> strtotime( Mage::getModel('core/date')->gmtDate() ) // current timestamp
			){
				continue;
			}

			$helper->log( 'processing ID: '. $unprocessed_order->getId() .' incrementID: '. $unprocessed_order->getIncrementId() );

			// Prepare the email object
			Mage::getSingleton('core/translate')->setTranslateInline(false);
			$mailTemplate 	= Mage::getModel('core/email_template');

			// Set admins to receive a copy
			$copyTo = $this->_getEmails( self::XML_PATH_EMAIL_COPY_TO );
			if( $copyTo ){
				foreach( $copyTo as $email ){
					$mailTemplate->addBcc( $email );
				}
			}

			// Determine the recipient
			$recipient = $unprocessed_order->getCustomerEmail();
			if( $email_override = Mage::getStoreConfig( self::XML_PATH_EMAIL_OVERRIDE ) ){
				$recipient = $email_override;
			}
			$helper->log( 'Recipient: '. $recipient );

			// Send the email!
			$mailTemplate->setDesignConfig( array( 'area' => 'frontend', 'store' => 1 ) )
				->sendTransactional(
					$email_template_id, // Email Template ID
					Mage::getStoreConfig( self::XML_PATH_EMAIL_IDENTITY ), // Sender
					$recipient, // Recipient Email
					$unprocessed_order->getCustomerName(), // Recipient Name
					array(
						'order' => $unprocessed_order,
						'storename' => Mage::app()->getStore()->getFrontendName()
					)
				);

			Mage::getSingleton('core/translate')->setTranslateInline(true);

			if( !$mailTemplate->getSentSuccess() ){
				$helper->log( 'ERROR - failed to send order followup email for order: '. $unprocessed_order->getId() );
			} else {

				// Log the fact that the email was sent so we don't continue to send in subsequent runs
				$log = Mage::getModel('orderfollowupemail/log')
					->setOrderId( $unprocessed_order->getId() )
					->setProcessedDate( Mage::getModel('core/date')->gmtDate() );
				$log->save();

			}

		}

		return;

	}

	protected function _getEmails( $configPath ){

		$data = Mage::getStoreConfig( $configPath );

		if( !empty( $data ) ){
			return explode( ',', $data );
		}

		return false;

	}

}

?>
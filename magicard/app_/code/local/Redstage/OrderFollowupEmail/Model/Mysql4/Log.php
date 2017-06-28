<?php

class Redstage_OrderFollowupEmail_Model_Mysql4_Log extends Mage_Core_Model_Mysql4_Abstract {

	public function _construct(){

		$this->_init('orderfollowupemail/log', 'log_id');

	}

}

?>
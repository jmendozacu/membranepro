<?php

class Redstage_OrderFollowupEmail_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function log( $string ){
		// write to file or database or whatever
		/*$stream = fopen( '/var/www/clients/client1/web4/web/tmp/orderfollowupemail.txt', 'a+' );
		fwrite( $stream, $string ."\n" );
		fclose( $stream );*/
	}

}

?>
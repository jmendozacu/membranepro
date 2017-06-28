<?php
        header("HTTP/1.1 503 Service Temporarily Unavailable");
        header("Status: 503 Service Temporarily Unavailable");
        header("Retry-After: 7200"); // 2 hours
		
		echo '<center><br/><br/><img src="503_logo.png" /><br/><br/>';
		echo 'Site-ul nostru este inchis temporar pentru mentenanta.<br/>Vom reveni in cel mai scurt timp.';
		echo '</center>';
?>
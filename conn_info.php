<?php
	$stats = amule_get_stats();
	if ( $stats["id"] == 0 ) {
		echo "Not connected";
	} elseif ( $stats["id"] == 0xffffffff ) {
		echo "Connecting ...";
	} else {
		echo "Connected with ", (($stats["id"] < 16777216) ? "low" : "high"), " ID to ",
			htmlspecialchars($stats["serv_name"]), "  ", htmlspecialchars($stats["serv_addr"]);
	}
?>
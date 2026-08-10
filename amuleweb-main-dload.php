<!doctype html>
<html>
<head>
	<title>aMule - Download</title>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="script.js"></script>

		<?php
			// Auto-refresh: reload on a timer, but skip while any checkbox is
			// checked so the user's selection isn't wiped.
			if ( $_SESSION["auto_refresh"] > 0 ) {
				echo "<script type=\"text/JavaScript\">
						setInterval(function() {
							if (document.querySelectorAll('input[type=\"checkbox\"]:checked').length > 0) {
								return;
							}
							window.location.href = window.location.href;
						}, 1000 * ", $_SESSION["auto_refresh"], ");
					</script>";
			}
		?>

	<script language="JavaScript" type="text/JavaScript">
		function formCommandSubmit(command)
		{
			if ( command == "cancel" ) {
				var boxchecked = document.querySelectorAll('input[type="checkbox"]:checked');
				var selectedFiles = Object.values(boxchecked).filter(selected => selected.name != 'selectAllFiles').length;
				if (selectedFiles == 0)
					return;
				var res = confirm("Delete selected " + (selectedFiles) + " files ?")
				if ( res == false ) {
					return;
				}
			}
			if ( command != "filter" ) {
				<?php
					if ($_SESSION["guest_login"] != 0) {
							echo 'alert("You logged in as guest - commands are disabled");';
							echo "return;";
					}
				?>
			}
			var frm=document.forms.mainform
			frm.command.value=command
			frm.submit()
		}

		function selectAll(check)
		{
			var checkboxes = document.querySelectorAll('input[type="checkbox"]');
			checkboxes.forEach(function(checkbox) {
				checkbox.checked = check.checked;
			});
		}
	</script>
	<link href="style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="layout">

        <div id="navigation-container"></div>

        <div class="content-area">

            <main class="main">

                <h1>Transferencias</h1>

				<div class="downloads-mobile">

					<?php 

					// Whitelist against the column keys my_cmp() actually understands
					// (the switch() above). Anything not in the list is dropped to "",
					// which falls through to the "no sort change" branch below.
					// This prevents an attacker-controlled value from being stored in
					// $_SESSION["download_sort"] and later reflected into rendered HTML
					// (#869 follow-up; same fix applied to shared and servers pages).
					$sort_raw = isset($HTTP_GET_VARS["sort"]) ? $HTTP_GET_VARS["sort"] : "";
					if ($sort_raw == "size" || $sort_raw == "size_done" || $sort_raw == "progress" ||
						$sort_raw == "name" || $sort_raw == "speed" || $sort_raw == "srccount" ||
						$sort_raw == "status" || $sort_raw == "prio") {
						$sort_order = $sort_raw;
					} else {
						$sort_order = "";
					}

					if ( $sort_order == "" ) {
						$sort_order = $_SESSION["download_sort"];
					} else {
						if ( $_SESSION["download_sort_reverse"] == "" ) {
							$_SESSION["download_sort_reverse"] = 0;
						} else {
							if ( $HTTP_GET_VARS["sort"] != '') {
								$_SESSION["download_sort_reverse"] = !$_SESSION["download_sort_reverse"];
							}
						}
					}
					//var_dump($_SESSION);
					$sort_reverse = $_SESSION["download_sort_reverse"];
					if ( $sort_order != "" ) {
						$_SESSION["download_sort"] = $sort_order;
						usort(&$downloads, "my_cmp");
					}

					$downloads = amule_load_vars("downloads");
					$fakevar = 0; 									

					function CastToXBytes($size, &$count) {
						// Emit the raw byte count; the unit formatting is done
						// client-side (see the js-size script at the end of the page).
						$count += $size;
						return '<span class="js-size">' . $size . '</span>';
					}

					function StatusString($file)
					{
						if ( $file->status == 7 ) {
							return "Paused";
						} elseif ( $file->src_count_xfer > 0 ) {
							return "Downloading";
						} else {
							return "Waiting";
						}
					}

					foreach ($downloads as $file) {

						$status = StatusString($file);

						$progress = 0;
						if ($file->size > 0) {
							$progress = ($file->size_done * 100) / $file->size;
						}

						echo'<div class="download-card">';

						echo'	<div class="download-card-top">';
						echo'		<div class="download-card-check">';
						echo'			<input type="checkbox" name="' . $file->hash . '">';
						echo'		</div>';
						
						echo'		<div class="download-card-name">' . htmlspecialchars($file->name) . '</div>';
						echo'	</div>';

						echo'	<div class="download-card-progress">';
						echo'		<div class="download-progress-bar" style="width: ' . $progress . '%"></div>';
						echo'	</div>';

						echo'	<div class="download-card-meta">';
						echo'		<span>' . (int)$progress . '%</span>';
						echo'		<span>' . CastToXBytes($file->size_done, $fakevar) . '/' . CastToXBytes($file->size, $fakevar) . '</span>';
						echo'	</div>';

						echo'	<div class="download-card-info">';
						echo'		<span>';
						echo'			<i class="fa-solid fa-download"></i>';
											($file->speed > 0)
											? CastToXBytes($file->speed, $fakevar) . "/s"
											: "-";								
						echo'		</span>';
						echo'		<span>';
						echo'			<i class="fa-solid fa-users"></i>' . $file->src_count;
						echo'		</span>';
						echo'		<span>' . htmlspecialchars($status) . '</span>';
						echo'	</div>';

						echo'</div>';

					} ?>
				
				</div>

				







	<script type="text/JavaScript">
		// Format the raw byte counts emitted by the backend (spans with class
		// "js-size") into human-readable units. Done here in the browser because
		// the webserver's PHP interpreter lacks sprintf/round.
		function formatBytes(value) {
			var b = parseFloat(value);
			if ( isNaN(b) ) return value;
			if ( b < 1024 ) return b + " Bytes";
			if ( b < 1048576 ) return (b / 1024).toFixed(2) + " KB";
			if ( b < 1073741824 ) return (b / 1048576).toFixed(2) + " MB";
			return (b / 1073741824).toFixed(2) + " GB";
		}
		(function() {
			var els = document.getElementsByClassName("js-size");
			for ( var i = 0; i < els.length; i++ ) {
				els[i].textContent = formatBytes(els[i].textContent);
			}
		})();

		loadComponent("navigation.php", "navigation-container");
		loadComponent("conn_info.php", "footer-stats");

		// refresco cada 10s (o usa $_SESSION["auto_refresh"] si quieres)
		setInterval(function() {
			loadComponent("conn_info.php", "footer-stats");
		}, 10000);
	</script>

</body>

</html>

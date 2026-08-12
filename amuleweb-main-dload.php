<!doctype html>
<html>
<head>
	<title>aMule - Download</title>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="script.js"></script>
	<script src="download.js"></script>

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
			var checkboxes = document.querySelectorAll(
				'.download-card input[type="checkbox"]'
			);

			checkboxes.forEach(function(checkbox) {
				checkbox.checked = check.checked;
			});

			updateDownloadSelection();
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

				<form action="amuleweb-main-dload.php" method="post" name="mainform" id="mainform">

					<input type="hidden" name="command" value="">					

						<?php 

							function renderDownloadMobile($file)
							{
								$status = StatusString($file);

								$progress = 0;
								if ($file->size > 0) {
									$progress = ($file->size_done * 100) / $file->size;
								}

								echo'<div class="download-card" data-status="' . htmlspecialchars($status) . '">';

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
								echo				($file->speed > 0)
													? CastToXBytes($file->speed, $fakevar) . "/s"
													: "-";								
								echo'		</span>';
								echo'		<span>';
								echo'			<i class="fa-solid fa-users"></i>' . $file->src_count;
								echo'		</span>';
								echo'		<span>' . htmlspecialchars($status) . '</span>';
								echo'	</div>';

								echo'</div>';
							}

							function renderDownloadDesktopHeader()
							{
								echo '<div class="downloads-header">

										<div class="download-header-check">
											<input type="checkbox" name="selectAllDownloads" onclick="selectAllDownloads(this)">
										</div>

										<div class="download-header-name">
											<a href="amuleweb-main-dload.php?sort=name">Nombre</a>
										</div>

										<div class="download-header-size">
											<a href="amuleweb-main-dload.php?sort=size">Tamaño</a>
										</div>

										<div class="download-header-completed">
											<a href="amuleweb-main-dload.php?sort=size_done">Completado</a>
										</div>

										<div class="download-header-speed">
											<a href="amuleweb-main-dload.php?sort=speed">Velocidad</a>
										</div>

										<div class="download-header-progress">
											<a href="amuleweb-main-dload.php?sort=progress">Progreso</a>
										</div>

										<div class="download-header-sources">
											<a href="amuleweb-main-dload.php?sort=srccount">Fuentes</a>
										</div>

										<div class="download-header-status">
											<a href="amuleweb-main-dload.php?sort=status">Estado</a>
										</div>
										
									</div>';
							}
							
							function renderDownloadDesktop($file)
							{
								$status = StatusString($file);

								$progress = 0;
								if ($file->size > 0) {
									$progress = ($file->size_done * 100) / $file->size;
								}

								$fakevar = 0;

								echo '<div class="download-row" data-status="' . htmlspecialchars($status) . '">';

								// Checkbox
								echo '    <div class="download-row-check">';
								echo '        <input type="checkbox" name="' . $file->hash . '">';
								echo '    </div>';

								// Nombre
								echo '    <div class="download-row-name">';
								echo          htmlspecialchars($file->name);
								echo '    </div>';

								// Tamaño
								echo '    <div class="download-row-size">';
								echo          CastToXBytes($file->size, $fakevar);
								echo '    </div>';

								// Completado
								echo '    <div class="download-row-completed">';
								echo          CastToXBytes($file->size_done, $fakevar);
								echo '    </div>';

								// Velocidad
								echo '    <div class="download-row-speed">';

								if ($file->speed > 0) {
									echo CastToXBytes($file->speed, $fakevar) . '/s';
								} else {
									echo '-';
								}

								echo '    </div>';

								// Progreso
								echo '    <div class="download-row-progress">';
								echo '        <div class="desktop-progress">';
								echo '            <div class="desktop-progress-bar" style="width:' . $progress . '%"></div>';
								echo '        </div>';
								echo '        <span>' . (int)$progress . '%</span>';
								echo '    </div>';

								// Fuentes
								echo '    <div class="download-row-sources">';
								echo '        <i class="fa-solid fa-users"></i> ';
								echo          (int)$file->src_count;
								echo '    </div>';

								// Estado
								echo '    <div class="download-row-status">';
								echo          htmlspecialchars($status);
								echo '    </div>';

								echo '</div>';
							}

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

							if ( ($HTTP_GET_VARS["command"] != "") && ($_SESSION["guest_login"] == 0) ) {
								foreach ( $HTTP_GET_VARS as $name => $val) {
									// this is file checkboxes
									if ( (strlen($name) == 32) and ($val == "on") ) {
										//var_dump($name);
										amule_do_download_cmd($name, $HTTP_GET_VARS["command"]);
									}
								}
							}

							$downloads = amule_load_vars("downloads");
							$fakevar = 0; 
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

							echo '<div class="downloads-mobile">';	

							foreach ($downloads as $file) {
								renderDownloadMobile($file);
							} 

							echo '</div>';

							echo '<div class="downloads-desktop">';

							echo '<div class="downloads-title">';
							echo '    <i class="fa-solid fa-download"></i>';
							echo '    <span>Descargas</span>';
							echo '</div>';

							renderDownloadDesktopHeader();

							foreach ($downloads as $file) {
								renderDownloadDesktop($file);
							}

							echo '</div>';
						?>

					<div id="mobileDownloadActions">

						<div class="mobile-download-selection">
							<i class="fa-solid fa-check"></i>
							<span id="downloadSelectedCount">0</span>
							<span>seleccionada(s)</span>
						</div>

						<div class="mobile-download-action-buttons">

							<button
								type="button"
								data-action="pause"
								class="mobile-download-action"
								id="mobilePauseButton"
								onclick="formCommandSubmit('pause')">
								<i class="fa-solid fa-pause"></i>
								<span>Pausar</span>
							</button>

							<button
								type="button"
								data-action="resume"
								class="mobile-download-action"
								id="mobileResumeButton"
								onclick="formCommandSubmit('resume')">
								<i class="fa-solid fa-play"></i>
								<span>Reanudar</span>
							</button>

							<button
								type="button"
								data-action="cancel"
								class="mobile-download-action mobile-download-action-danger"
								onclick="formCommandSubmit('cancel')">
								<i class="fa-solid fa-xmark"></i>
								<span>Cancelar</span>
							</button>

						</div>

					</div>					

				</form>

			</main>

			<div class="footer">
                <div class="stats" id="footer-stats">
                    Cargando...
                </div>
                <div class="brand">
                    aMule Web UI custom
                </div>
            </div>

		</div>

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
		
		document.querySelectorAll('.download-card').forEach(card => {

		card.addEventListener('click', function (e) {

			// Si hacemos click en el checkbox, no hacemos nada más (el evento ya se ha disparado)
			if (e.target.tagName.toLowerCase() === 'input') {
				updateDownloadSelection();
				return;
			}

			const checkbox = card.querySelector('input[type="checkbox"]');

			if (checkbox) {
				checkbox.checked = !checkbox.checked;

				if (checkbox.checked) {
					card.classList.add("selected");
				} else {
					card.classList.remove("selected");
				}

				updateDownloadSelection();
			}

		});

	});

	</script>

</body>

</html>

<!DOCTYPE html>
<html>
<head>
    <title>aMule control panel</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    if ($_SESSION["auto_refresh"] > 0) {
        echo "<script>
            setInterval(function() {
                if (document.querySelectorAll('input[type=\"checkbox\"]:checked').length > 0) return;
                location.reload();
            }, " . (1000 * $_SESSION["auto_refresh"]) . ");
        </script>";
    }
    ?>
	
	<script language="JavaScript" type="text/JavaScript">
		function formCommandSubmit(command)
		{
			<?php
				if ($_SESSION["guest_login"] != 0) {
						echo 'alert("You logged in as guest - commands are disabled");';
						echo "return;";
				}
			?>
			if ( command == "download" ) {
				var boxchecked = document.querySelectorAll('input[type="checkbox"]:checked');
				var selectedFiles = Object.values(boxchecked).filter(selected => selected.name != 'selectAllFiles').length;
				if (selectedFiles == 0)
					return;
				var res = confirm("Download selected " + (selectedFiles) + " files ?")
				if ( res == false ) {
					return;
				}
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

		<div class="menu-toggle">
			<button onclick="toggleMenu()">
				<i class="fa fa-bars"></i>
			</button>
		</div>

		<div class="sidebar" id="sidebar">
		
			<div id="logo">
				
			</div>
		
			<div class="sidebar-menu">

				<a href="amuleweb-main-dload.php">
					<i class="fa fa-download"></i> Transferencias
				</a>

				<a href="amuleweb-main-search.php">
					<i class="fa fa-search"></i> Buscar
				</a>

				<a href="amuleweb-main-servers.php">
					<i class="fa fa-server"></i> Servidores
				</a>

				<a href="amuleweb-main-kad.php">
					<i class="fa fa-link"></i> Kad
				</a>

				<a href="amuleweb-main-stats.php">
					<i class="fa fa-chart-bar"></i> Estadísticas
				</a>

				<a href="amuleweb-main-prefs.php">
					<i class="fa fa-cog"></i> Configuración
				</a>

				<a href="login.php">
					<i class="fa fa-sign-out-alt"></i> Salir
				</a>

			</div>

		</div>

		<script>
			function toggleMenu()
			{
				var menu = document.getElementById("sidebar");

				if (menu.className.indexOf("open") == -1) {
					menu.className += " open";
				} else {
					menu.className = menu.className.replace(" open","");
				}
			}
		</script>
		
		<div class="content-area">

			<main class="main">

				<h1>Buscar</h1>

				<form name="mainform" action="amuleweb-main-search.php" method="post" class="search-box">

					<input type="hidden" name="command" value="" />

					<div class="search-row">
					
						<div class="search-wrapper">
							<input id="searchInput" name="searchval" type="text" placeholder="Buscar..." class="search-input" />
							<span class="clear-btn" id="clearBtn">×</span>
						</div>
							
						<select name="searchtype">
							<option selected>Local</option>
							<option>Global</option>
							<option>Kad</option>
						</select>
						<button class="btn" onclick="formCommandSubmit('search')">Search</button>
					</div>

					<div class="filters">
						
					</div>
					
					<div class="actions">
						<button class="btn" onclick="formCommandSubmit('download'); return false;">Download</button>
						<button class="btn secondary" type="button" onclick="window.location.href='amuleweb-main-search.php'">Update</button>
					</div>
						
					<!-- RESULTS -->
					<div class="results">

						<?php				
							function CastToXBytes($size)
							{
								// Emit the raw byte count; the unit formatting is done
								// client-side (see the js-size script at the end of the page).
								return '<span class="js-size">' . $size . '</span>';
							}

							//
							// declare it here, before any function reffered it in "global"
							//
							$sort_order;$sort_reverse;

							function my_cmp($a, $b)
							{
								global $sort_order, $sort_reverse;
								
								switch ( $sort_order) {
									case "size": $result = $a->size > $b->size; break;
									case "name": $result = $a->name > $b->name; break;
									case "sources": $result = $a->sources > $b->sources; break;
								}

								if ( $sort_reverse ) {
									$result = !$result;
								}

								return $result;
							}

							function str2mult($str)
							{
								$result = 1;
								switch($str) {
									case "Byte":	$result = 1; break;
									case "KByte":	$result = 1024; break;		
									case "MByte":	$result = 1024*1024; break;
									case "GByte":	$result = 1024*1024*1024; break;
								}
								return $result;
							}

							function cat2idx($cat)
							{
								$cats = amule_get_categories();
								$result = 0;
								foreach($cats as $i => $c) {
									if ( $cat == $c) $result = $i;
								}
								return $result;
							}

							if ($_SESSION["guest_login"] == 0) {
								if ( $HTTP_GET_VARS["command"] == "search") {
									$search_type = -1;
									switch($HTTP_GET_VARS["searchtype"]) {
										case "Local": $search_type = 0; break;
										case "Global": $search_type = 1; break;
										case "Kad": $search_type = 2; break;
									}
									
									amule_do_search_start_cmd($HTTP_GET_VARS["searchval"], "", "", $search_type, "", "", "");
									
								} elseif ( $HTTP_GET_VARS["command"] == "download") {
									foreach ( $HTTP_GET_VARS as $name => $val) {
										// this is file checkboxes
										if ( (strlen($name) == 32) and ($val == "on") ) {
											$cat = $HTTP_GET_VARS["targetcat"];
											$cat_idx = cat2idx($cat);
											amule_do_search_download_cmd($name, $cat_idx);
										}
									}
								} else {
								}
							}		
							$search = amule_load_vars("searchresult");

							// Column-header links use ?sort=<key> and TOGGLE the sort
							// direction on each click. Any request without a (valid) sort
							// key — including the "Update results" refresh, which just
							// reloads the page — falls through to the order remembered in
							// the session, so refreshing the streamed results keeps the
							// current order without flipping it. The key is whitelisted
							// against the keys my_cmp() understands, same pattern as the
							// dload/shared/servers pages: this prevents an attacker-
							// controlled value from being stored in $_SESSION["search_sort"]
							// and later reflected into rendered HTML (#869 follow-up).
							$sort_raw = isset($HTTP_GET_VARS["sort"]) ? $HTTP_GET_VARS["sort"] : "";
							if ($sort_raw == "size" || $sort_raw == "name" || $sort_raw == "sources") {
								$sort_order = $sort_raw;
								if ( $_SESSION["search_sort_reverse"] == "" ) {
									$_SESSION["search_sort_reverse"] = 0;
								} else {
									$_SESSION["search_sort_reverse"] = !$_SESSION["search_sort_reverse"];
								}
							} else {
								$sort_order = $_SESSION["search_sort"];
							}

							$sort_reverse = $_SESSION["search_sort_reverse"];
							if ( $sort_order != "" ) {
								$_SESSION["search_sort"] = $sort_order;
								usort(&$search, "my_cmp");
							}
						
							if (count($search) > 0) {
								
								echo "<div class='results-header'>";
								echo "	<div class='select-all'><input type='checkbox' name='selectAllFiles'onclick='selectAll(this)' /></div>";
								echo "	<div class='sort-name'><a href='amuleweb-main-search.php?sort=name'>Nombre</a></div>";
								echo "	<div class='sort-size'><a href='amuleweb-main-search.php?sort=size'>Tamaño</a></div>";
								echo "	<div class='sort-sources'><a href='amuleweb-main-search.php?sort=sources'>Fuentes</a></div>";
								echo "</div>";
						
								foreach ($search as $file) {
									echo "<div class='card'>";

									// checkbox
									echo "<div class='card-check'>";
									echo "<input type='checkbox' name='", $file->hash, "'>";
									echo "</div>";

									// nombre
									echo "<div class='card-main'>";
									echo "<div class='file-name'>", htmlspecialchars($file->name), "</div>";
									echo "</div>";

									// tamano
									echo "<div class='card-size'>";
									echo CastToXBytes($file->size);
									echo "</div>";
									
									// fuentes
									echo "<div class='card-sources'>";
									echo "<i class='fa-solid fa-user-group'></i> ", $file->sources;
									echo "</div>";

									echo "</div>";
								}
							}
						?>						

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
			<script>
				function loadStats() {

					fetch("conn_info.php")
						.then(function(r) {
							return r.text();
						})
						.then(function(html) {
							document.getElementById("footer-stats").innerHTML = html;
						})
						.catch(function() {
							document.getElementById("footer-stats").innerHTML = "Error cargando stats";
						});
				} 

				// primera carga
				loadStats();

				// refresco cada 10s (o usa $_SESSION["auto_refresh"] si quieres)
				setInterval(loadStats, 1000);
				
				const current = window.location.pathname.split('/').pop();

				document.querySelectorAll('.sidebar a').forEach(link => {
				  const href = link.getAttribute('href');

				  if (href === current) {
					link.classList.add('active');
				  } else {
					link.classList.remove('active');
				  }
				});
			</script>
			
		</div>

	</div> <!-- layout -->
	
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
		
		document.querySelectorAll('.card').forEach(card => {
		  card.addEventListener('click', (e) => {
			// Evitar doble toggle si se hace click directamente en el checkbox
			if (e.target.tagName.toLowerCase() === 'input') return;

			const checkbox = card.querySelector('input[type="checkbox"]');
			if (checkbox) {
			  checkbox.checked = !checkbox.checked;
			}

			card.classList.toggle('selected', checkbox.checked);
		  });
		});
		
		const input = document.getElementById('searchInput');
		const clearBtn = document.getElementById('clearBtn');

		input.addEventListener('input', () => {
		  clearBtn.style.display = input.value ? 'block' : 'none';
		});

		clearBtn.addEventListener('click', () => {
		  input.value = '';
		  input.focus();
		  clearBtn.style.display = 'none';
		});
		
	</script>
	
</body>
</html>
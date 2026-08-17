<!doctype html>
<html>
<head>
	<title>aMule - Download</title>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="script.js"></script>
	<script src="server.js"></script>

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

	<link href="style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="layout">

        <div id="navigation-container"></div>

        <div class="content-area">

            <main class="main">

                <h1>Servidores</h1>

				<div class="servers-results">

				<?php  

					function renderServerHeader()
					{

						echo '<div class="servers-header">

								<div class="server-header-connect"></div>

								<div class="server-header-name">
									<a href="amuleweb-main-servers.php?sort=name">Servidor</a>
								</div>

								<div class="server-header-description">Descripción</div>

								<div class="server-header-address">Dirección</div>

								<div class="serversheader-users">
									<a href="amuleweb-main-servers.php?sort=users">Usuarios</a>
								</div>

								<div class="server-header-files">
									<a href="amuleweb-main-servers.php?sort=files">Archivos</a>
								</div>
								
							</div>';
					}
					
					function renderServerDesktop($server)
					{
						echo '<div class="server-row">
						
							<div class="server-row-connect">
								<a href="amuleweb-main-servers.php?cmd=connect&ip=' . $server->ip . '&port=' . $server->port . '"
									class="server-action">
									<i class="fa-solid fa-plug"></i>
								</a>
							
								<a href="amuleweb-main-servers.php?cmd=remove&ip=' . $server->ip . '&port=' . $server->port . '"
									class="server-action server-action-danger">
									<i class="fa-regular fa-trash-can"></i>
								</a>
							</div>
							
							<div class="server-row-name">' . htmlspecialchars($server->name) . '</div>
							
							<div class="server-row-description">' . htmlspecialchars($server->desc) . '</div>
							
							<div class="server-row-address">' . htmlspecialchars($server->addr) . '</div>
							
							<div class="server-row-users">
								<i class="fa-solid fa-users"></i> ' . $server->users . '
							</div>
							
							<div class="server-row-files">
								<i class="fa-solid fa-photo-film"></i> ' . $server->files . '
							</div>

						</div>';
					}

					function renderServerMobile($srv, $connected)
					{
						$connectedClass = $connected ? " connected" : "";

						echo '<div
								class="server-card"
								data-ip="' . htmlspecialchars($srv->ip) . '"
								data-port="' . htmlspecialchars($srv->port) . '">';

						echo '    <div class="server-card-top">';

						echo '        <div class="server-card-name">';
						echo              htmlspecialchars($srv->name);
						echo '        </div>';

						if ($connected) {
							echo '        <div class="server-card-connected">';
							echo '            <i class="fa-solid fa-check"></i>';
							echo '            <span>Conectado</span>';
							echo '        </div>';
						}

						echo '    </div>';

						echo '    <div class="server-card-description">';
						echo          htmlspecialchars($srv->desc);
						echo '    </div>';

						echo '    <div class="server-card-address">';
						echo          htmlspecialchars($srv->addr);
						echo '    </div>';

						echo '    <div class="server-card-info">';

						echo '        <span>';
						echo '            <i class="fa-solid fa-users"></i>';
						echo              (int)$srv->users;
						echo '        </span>';

						echo '        <span>';
						echo '            <i class="fa-solid fa-file"></i>';
						echo              (int)$srv->files;
						echo '        </span>';

						echo '    </div>';

						echo '</div>';
					}

					function renderMobileServerActions() {

						echo '	<div id="mobileServerActions">

									<div class="mobile-server-action-buttons">

										<button
											type="button"
											class="mobile-server-action"
											id="mobileServerConnectButton">
											<i class="fa-solid fa-plug"></i>
											<span>Conectar</span>
										</button>

										<button
											type="button"
											class="mobile-server-action mobile-server-action-danger"
											id="mobileServerRemoveButton">
											<i class="fa-solid fa-trash"></i>
											<span>Eliminar</span>
										</button>

									</div>

								</div>';
					}

					$sort_order;$sort_reverse;

					function my_cmp($a, $b)
					{
						global $sort_order, $sort_reverse;
						switch ( $sort_order) {
							case "name": $result = $a->name > $b->name; break;
							case "desc": $result = $a->desc > $b->desc; break;
							case "users": $result = $a->users > $b->users; break;
							case "files":$result = $a->files > $b->files; break;
						}

						if ( $sort_reverse ) {
							$result = !$result;
						}
						return $result;
					}
				
					$servers = amule_load_vars("servers");

					// Whitelist against the column keys my_cmp() actually understands
					// (the switch() above). Anything not in the list is dropped to "",
					// which falls through to the "no sort change" branch below (#869 follow-up).
					$sort_raw = isset($HTTP_GET_VARS["sort"]) ? $HTTP_GET_VARS["sort"] : "";
					if ($sort_raw == "name" || $sort_raw == "desc" ||
						$sort_raw == "users" || $sort_raw == "files") {
						$sort_order = $sort_raw;
					} else {
						$sort_order = "";
					}

					//
					// perform command before processing content
					//
					if ( ($HTTP_GET_VARS["cmd"] != "") and ($HTTP_GET_VARS["ip"] != "") and ($HTTP_GET_VARS["port"] != "")) {
						if ($_SESSION["guest_login"] == 0) {
							amule_do_server_cmd($HTTP_GET_VARS["ip"], $HTTP_GET_VARS["port"], $HTTP_GET_VARS["cmd"]);
						}
					}
					// Network-level disconnect (no per-server target). The
					// amule_do_server_cmd path above always carries an ip/port
					// tag, so it can only target one server at a time.
					if ($HTTP_GET_VARS["server_action"] == "disconnect") {
						if ($_SESSION["guest_login"] == 0) {
							amule_server_disconnect();
						}
					}
					
					if ( $sort_order == "" ) {
						$sort_order = $_SESSION["servers_sort"];
					} else {
						if ( $_SESSION["sort_reverse"] == "" ) {
							$_SESSION["sort_reverse"] = 0;
						} else {
							$_SESSION["sort_reverse"] = !$_SESSION["sort_reverse"];
						}
					}

					$sort_reverse = $_SESSION["sort_reverse"];
					if ( $sort_order != "" ) {
						$_SESSION["servers_sort"] = $sort_order;
						usort(&$servers, "my_cmp");
					}

					$stats = amule_get_stats();

					$connectedServerName = "";
					$connectedServerAddr = "";

					if ($stats["id"] != 0 && $stats["id"] != 0xffffffff) {
						$connectedServerName = $stats["serv_name"];
						$connectedServerAddr = $stats["serv_addr"];
					}

					echo '<div class="servers-desktop">';

					renderServerHeader();

					foreach ($servers as $srv) {
						renderServerDesktop($srv);		
					}

					echo '</div>';

					echo '<div class="servers-mobile">';

					foreach ($servers as $srv) {
						$connected = ($connectedServerAddr != "" && ('[' . $srv->addr . ']') == $connectedServerAddr);

						renderServerMobile($srv, $connected);
					}

					echo '</div>';

					echo '</div>'; //servers-results

					renderMobileServerActions();

				?>

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

		loadComponent("navigation.php", "navigation-container");
		loadComponent("conn_info.php", "footer-stats");

		setInterval(function() {
			loadComponent("conn_info.php", "footer-stats");
		}, 1000);

	</script>

</body>

</html>
		<div class="footer">
			<div class="stats">
				<?php
					$stats = amule_load_vars("stats");

					if ( $stats["id"] == 0 ) {
						echo "<span>Sin conexión</span>";
					} elseif ( $stats["id"] == 0xffffffff ) {
						echo "<span>Conectando...</span>";
					} else {
						echo "<span>Conectado con ", (($stats["id"] < 16777216) ? "low" : "high"), " ID al servidor: " . htmlspecialchars($stats->server_name) . "</span>";
					} 
				?>
			</div>

			<div class="brand">
				aMule Web UI custom
			</div>
		</div>
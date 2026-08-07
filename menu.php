<?php
$current = "";
if (isset($_SERVER["PHP_SELF"])) {
    $current = basename($_SERVER["PHP_SELF"]);
}
?>

<div class="mobile-topbar">
    <button onclick="toggleMenu()">
        <i class="fa fa-bars"></i>
    </button>
    <span>aMule</span>
</div>


<div class="sidebar" id="sidebar">

<nav>

<a href="amuleweb-main-dload.php" 
<?php if ($current == "amuleweb-main-dload.php") echo 'class="active"'; ?>>
<i class="fa fa-download"></i> Transferencias
</a>


<a href="amuleweb-main-search.php"
<?php if ($current == "amuleweb-main-search.php") echo 'class="active"'; ?>>
<i class="fa fa-search"></i> Buscar
</a>


<a href="amuleweb-main-servers.php"
<?php if ($current == "amuleweb-main-servers.php") echo 'class="active"'; ?>>
<i class="fa fa-server"></i> Servidores
</a>


<a href="amuleweb-main-kad.php"
<?php if ($current == "amuleweb-main-kad.php") echo 'class="active"'; ?>>
<i class="fa fa-link"></i> Kad
</a>


<a href="amuleweb-main-stats.php"
<?php if ($current == "amuleweb-main-stats.php") echo 'class="active"'; ?>>
<i class="fa fa-chart-bar"></i> Estadísticas
</a>


<a href="amuleweb-main-prefs.php"
<?php if ($current == "amuleweb-main-prefs.php") echo 'class="active"'; ?>>
<i class="fa fa-cog"></i> Configuración
</a>


<a href="login.php">
<i class="fa fa-sign-out-alt"></i> Salir
</a>


</nav>

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
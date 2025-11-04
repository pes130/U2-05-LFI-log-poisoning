<?php
$allowed_dir = __DIR__ . '/pages/';
$page = isset($_GET['page']) ? $_GET['page'] : 'main.php';

$ruta_absoluta = $allowed_dir . $page;

include(__DIR__ . '/includes/header.php');

echo "<span style='font-size:x-small; color:gray'>".$ruta_absoluta."</span>";
if ($ruta_absoluta) {
    include($ruta_absoluta);
} else {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Página no encontrada o acceso denegado.</div></div>";
}
include(__DIR__ . '/includes/footer.php');
?>

<?php
// CTF LFI demo - intentionally vulnerable
$allowed_dir = __DIR__ . '/pages/';
$page = isset($_GET['page']) ? $_GET['page'] : 'about.php';

// Small filter you can comment to make LFI easier:
// $page = str_replace("..", "", $page);
// $page = basename($page);

$full = realpath($allowed_dir . $page);

include(__DIR__ . '/includes/header.php');

if ($full && str_starts_with($full, realpath($allowed_dir))) {
    include($full);
} else {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Página no encontrada o acceso denegado.</div></div>";
}

include(__DIR__ . '/includes/footer.php');
?>


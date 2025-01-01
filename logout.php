<?php
session_start();
session_unset(); // Șterge toate variabilele de sesiune
session_destroy(); // Distruge sesiunea

// Redirecționează către pagina principală
header('Location: index.php');
exit;
?>
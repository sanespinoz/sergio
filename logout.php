<?php
session_start();
unset($_SESSION['usuario']);
session_destroy();
// lo redirijo al login
header("Location:login.php");
die();
?>

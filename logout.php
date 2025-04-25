<?php
session_start();
print_r($_SESSION);
unset($_SESSION["user"]);
// session_destroy();
header("Location: login.php");
// exit;

<?php

require "../api/session.php";

/* destroy session */

$_SESSION = [];

session_unset();
session_destroy();

/* prevent cache */

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

/* redirect */

header("Location: ../login.html");
exit;
?>
<?php
// api/session.php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* ==========================
   DATABASE CONNECTIONS
========================== */

$server   = "localhost";
$username = "root";
$password = "";
$port     = 3307;

/* ---------- MASTER DB ---------- */

$con_master = mysqli_connect(
    $server,
    $username,
    $password,
    "master",
    $port
);

if(!$con_master){
    die("Master DB Failed: " . mysqli_connect_error());
}


/* ---------- COMPANY DB ---------- */

$con_company = mysqli_connect(
    $server,
    $username,
    $password,
    "company",
    $port
);

if(!$con_company){
    die("Company DB Failed: " . mysqli_connect_error());
}


/* ==========================
   AUTH CHECK
========================== */

$current = basename($_SERVER['PHP_SELF']);

$allowed = [
    "login.php",
    "register.php",
    "login.html",
    "register.html"
];

if(!in_array($current, $allowed)){

    if(!isset($_SESSION['user_id'])){

        header("Content-Type: application/json");

        echo json_encode([
            "status"  => "error",
            "message" => "Unauthorized access"
        ]);

        exit;
    }
}

/* default connection = master */

$con = $con_master;

?>

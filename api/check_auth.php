<?php
session_start();

header("Content-Type: application/json");

/* ==========================
   DATABASE CONNECTIONS
========================== */

$server   = "localhost";
$username = "root";
$password = "";
$port     = 3307;

/* MASTER DB */

$con_master = mysqli_connect(
    $server,
    $username,
    $password,
    "master",
    $port
);

if(!$con_master){
    die("Master DB Failed");
}


/* COMPANY DB */

$con_company = mysqli_connect(
    $server,
    $username,
    $password,
    "company",
    $port
);

if(!$con_company){
    die("Company DB Failed");
}


/* ==========================
   CHECK LOGIN
========================== */

if(isset($_SESSION['user_id'])){

    $id = $_SESSION['user_id'];

    // verify user exists in company DB
    $sql = "SELECT id FROM users WHERE id = $id";

    $res = mysqli_query($con_company, $sql);

    if($res && mysqli_num_rows($res) > 0){

        echo json_encode([
            "logged_in" => true,
            "selected_company_id" => $_SESSION['selected_company_id'] ?? null,
            "selected_company_code" => $_SESSION['selected_company_code'] ?? null,
            "selected_company_name" => $_SESSION['selected_company_name'] ?? null
        ]);

    }else{

        session_destroy();

        echo json_encode([
            "logged_in" => false
        ]);
    }

}else{

    echo json_encode([
        "logged_in" => false
    ]);
}
?>

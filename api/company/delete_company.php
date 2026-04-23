<?php
header("Content-Type: application/json");
require "../session.php";

$id = (int)($_POST["company_id"] ?? 0);

if(!$id){
    echo json_encode(["status"=>"error"]);
    exit;
}

/* check division */

$chk = mysqli_query(
    $con_company,
    "SELECT division_id
     FROM division_master
     WHERE company_id=$id
     LIMIT 1"
);

if(mysqli_fetch_assoc($chk)){
    echo json_encode([
        "status"=>"error",
        "message"=>"Company has divisions"
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $con_company,
    "DELETE FROM company_master
     WHERE company_id=?"
);

mysqli_stmt_bind_param($stmt,"i",$id);

mysqli_stmt_execute($stmt);

echo json_encode(["status"=>"success"]);
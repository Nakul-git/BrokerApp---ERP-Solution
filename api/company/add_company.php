<?php
header("Content-Type: application/json");
require "../session.php";

$user_id = (int)($_SESSION['user_id'] ?? 0);

function v($k){
    return trim($_POST[$k] ?? '');
}

$company_name = v("company_name");
$start_date   = v("start_date");
$end_date     = v("end_date");
$file_name    = v("file_name");

$is_default = isset($_POST["is_default"]) ? 1 : 0;
$is_active  = isset($_POST["is_active"]) ? 1 : 0;

if($company_name === ""){
    echo json_encode(["status"=>"error","message"=>"Name required"]);
    exit;
}


/* duplicate */

$q = mysqli_prepare(
    $con_company,
    "SELECT company_id
     FROM company_master
     WHERE LOWER(company_name)=LOWER(?)"
);

mysqli_stmt_bind_param($q,"s",$company_name);
mysqli_stmt_execute($q);

$r = mysqli_stmt_get_result($q);

if(mysqli_fetch_assoc($r)){
    echo json_encode([
        "status"=>"error",
        "message"=>"Exists"
    ]);
    exit;
}


/* reset default */

if($is_default){

    mysqli_query(
        $con_company,
        "UPDATE company_master SET is_default=0"
    );
}


/* insert */

$stmt = mysqli_prepare(
    $con_company,
    "INSERT INTO company_master
    (
        company_name,
        start_date,
        end_date,
        file_name,
        is_default,
        is_active,
        created_by
    )
    VALUES(?,?,?,?,?,?,?)"
);

mysqli_stmt_bind_param(
    $stmt,
    "ssssiii",
    $company_name,
    $start_date,
    $end_date,
    $file_name,
    $is_default,
    $is_active,
    $user_id
);

if(mysqli_stmt_execute($stmt)){
    echo json_encode([
        "status"=>"success"
    ]);
}else{
    echo json_encode([
        "status"=>"error"
    ]);
}
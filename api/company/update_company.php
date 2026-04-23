<?php
header("Content-Type: application/json");
require "../session.php";

$id = (int)$_POST["company_id"];

function v($k){
    return trim($_POST[$k] ?? '');
}

$name = v("company_name");
$start = v("start_date");
$end = v("end_date");
$file = v("file_name");

$is_default = isset($_POST["is_default"]) ? 1 : 0;
$is_active  = isset($_POST["is_active"]) ? 1 : 0;


if($is_default){
    mysqli_query(
        $con_company,
        "UPDATE company_master SET is_default=0"
    );
}

$stmt = mysqli_prepare(
    $con_company,
    "UPDATE company_master
     SET
        company_name=?,
        start_date=?,
        end_date=?,
        file_name=?,
        is_default=?,
        is_active=?
     WHERE company_id=?"
);

mysqli_stmt_bind_param(
    $stmt,
    "ssssiii",
    $name,
    $start,
    $end,
    $file,
    $is_default,
    $is_active,
    $id
);

mysqli_stmt_execute($stmt);

echo json_encode(["status"=>"success"]);
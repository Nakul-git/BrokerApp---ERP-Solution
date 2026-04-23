<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$product_group_name = trim($_POST['product_group_name'] ?? '');

if ($product_group_name === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Description required"
    ]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    "SELECT product_group_id
     FROM product_group_master
     WHERE user_id=? AND LOWER(product_group_name)=LOWER(?)
     LIMIT 1"
);
mysqli_stmt_bind_param($dup, "is", $user_id, $product_group_name);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        "status" => "error",
        "message" => "Product group already exists"
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    "INSERT INTO product_group_master (product_group_name, user_id)
     VALUES (?, ?)"
);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "si", $product_group_name, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "Product group added"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
}
?>

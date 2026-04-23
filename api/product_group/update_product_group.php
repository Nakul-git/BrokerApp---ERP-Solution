<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$product_group_id = (int)($_POST['product_group_id'] ?? 0);
$product_group_name = trim($_POST['product_group_name'] ?? '');

if ($product_group_id <= 0 || $product_group_name === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid data"
    ]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    "SELECT product_group_id
     FROM product_group_master
     WHERE user_id=? AND LOWER(product_group_name)=LOWER(?) AND product_group_id<>?
     LIMIT 1"
);
mysqli_stmt_bind_param($dup, "isi", $user_id, $product_group_name, $product_group_id);
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
    "UPDATE product_group_master
     SET product_group_name=?
     WHERE product_group_id=? AND user_id=?"
);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Update failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "sii", $product_group_name, $product_group_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "Updated"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Update failed"
    ]);
}
?>

<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$product_group_id = (int)($_POST['product_group_id'] ?? 0);
$user_id = get_master_scope_user_id();

if ($product_group_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid product group"
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    "DELETE FROM product_group_master
     WHERE product_group_id=? AND user_id=?"
);
mysqli_stmt_bind_param($stmt, "ii", $product_group_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "Deleted"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Delete failed"
    ]);
}
?>

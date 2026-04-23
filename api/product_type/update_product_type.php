<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$product_type_id = (int)($_POST['product_type_id'] ?? 0);
$description = trim($_POST['description'] ?? '');
$material_type = trim($_POST['material_type'] ?? '');

$valid_material_types = [
    "Raw Material",
    "Finished Goods",
    "Part Product",
    "Packing Material",
    "Consumable Material",
    "Bardana",
    "Other"
];

if ($product_type_id <= 0 || $description === '' || $material_type === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid data"
    ]);
    exit;
}

if (!in_array($material_type, $valid_material_types, true)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid material type"
    ]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    "SELECT product_type_id
     FROM product_type_master
     WHERE user_id=? AND LOWER(description)=LOWER(?) AND product_type_id<>?
     LIMIT 1"
);
mysqli_stmt_bind_param($dup, "isi", $user_id, $description, $product_type_id);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        "status" => "error",
        "message" => "Product type already exists"
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    "UPDATE product_type_master
     SET description=?, material_type=?
     WHERE product_type_id=? AND user_id=?"
);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Update failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "ssii", $description, $material_type, $product_type_id, $user_id);

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

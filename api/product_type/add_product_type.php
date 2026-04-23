<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
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

if ($description === '' || $material_type === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Description and material type required"
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
     WHERE user_id=? AND LOWER(description)=LOWER(?)
     LIMIT 1"
);
mysqli_stmt_bind_param($dup, "is", $user_id, $description);
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
    "INSERT INTO product_type_master (description, material_type, user_id)
     VALUES (?, ?, ?)"
);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "ssi", $description, $material_type, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "Product type added"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
}
?>

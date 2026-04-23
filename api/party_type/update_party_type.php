<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$party_type_id = (int)($_POST['party_type_id'] ?? 0);
$party_type_name = trim($_POST['party_type_name'] ?? '');
$party_type_code = strtoupper(trim($_POST['party_type_code'] ?? ''));

if ($party_type_id <= 0 || $party_type_name === '' || $party_type_code === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid data"
    ]);
    exit;
}

$dupName = mysqli_prepare(
    $con,
    "SELECT party_type_id FROM party_type_master WHERE user_id=? AND LOWER(party_type_name)=LOWER(?) AND party_type_id<>? LIMIT 1"
);
mysqli_stmt_bind_param($dupName, "isi", $user_id, $party_type_name, $party_type_id);
mysqli_stmt_execute($dupName);
$dupNameRes = mysqli_stmt_get_result($dupName);

if ($dupNameRes && mysqli_fetch_assoc($dupNameRes)) {
    echo json_encode([
        "status" => "error",
        "message" => "Party Type already exists"
    ]);
    exit;
}

$dupCode = mysqli_prepare(
    $con,
    "SELECT party_type_id FROM party_type_master WHERE user_id=? AND UPPER(party_type_code)=UPPER(?) AND party_type_id<>? LIMIT 1"
);
mysqli_stmt_bind_param($dupCode, "isi", $user_id, $party_type_code, $party_type_id);
mysqli_stmt_execute($dupCode);
$dupCodeRes = mysqli_stmt_get_result($dupCode);

if ($dupCodeRes && mysqli_fetch_assoc($dupCodeRes)) {
    echo json_encode([
        "status" => "error",
        "message" => "Party Type code already exists"
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    "UPDATE party_type_master
     SET party_type_name=?, party_type_code=?
     WHERE party_type_id=? AND user_id=?"
);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Update failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "ssii", $party_type_name, $party_type_code, $party_type_id, $user_id);

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

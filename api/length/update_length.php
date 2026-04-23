<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$length_id = (int)($_POST['length_id'] ?? 0);
$length_name = trim($_POST['length_name'] ?? '');

if ($length_id <= 0 || $length_name === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid data"
    ]);
    exit;
}

$dup = mysqli_prepare($con, "SELECT length_id FROM length_master WHERE user_id=? AND LOWER(length_name)=LOWER(?) AND length_id<>? LIMIT 1");
mysqli_stmt_bind_param($dup, "isi", $user_id, $length_name, $length_id);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        "status" => "error",
        "message" => "Length already exists"
    ]);
    exit;
}

$stmt = mysqli_prepare($con, "UPDATE length_master SET length_name=? WHERE length_id=? AND user_id=?");

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Update failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "sii", $length_name, $length_id, $user_id);

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

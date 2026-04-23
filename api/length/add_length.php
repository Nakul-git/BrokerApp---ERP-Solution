<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$length_name = trim($_POST['length_name'] ?? '');

if ($length_name === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Description required"
    ]);
    exit;
}

$dup = mysqli_prepare($con, "SELECT length_id FROM length_master WHERE user_id=? AND LOWER(length_name)=LOWER(?) LIMIT 1");
mysqli_stmt_bind_param($dup, "is", $user_id, $length_name);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        "status" => "error",
        "message" => "Length already exists"
    ]);
    exit;
}

$stmt = mysqli_prepare($con, "INSERT INTO length_master (length_name, user_id) VALUES (?, ?)");

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "si", $length_name, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "Length added"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
}
?>

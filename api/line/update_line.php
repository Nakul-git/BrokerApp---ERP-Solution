<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$line_id = (int)($_POST['line_id'] ?? 0);
$line_name = trim($_POST['line_name'] ?? '');

if ($line_id <= 0 || $line_name === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid data"
    ]);
    exit;
}

$dup = mysqli_prepare($con, "SELECT line_id FROM line_master WHERE user_id=? AND LOWER(line_name)=LOWER(?) AND line_id<>? LIMIT 1");
mysqli_stmt_bind_param($dup, "isi", $user_id, $line_name, $line_id);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        "status" => "error",
        "message" => "Line already exists"
    ]);
    exit;
}

$stmt = mysqli_prepare($con, "UPDATE line_master SET line_name=? WHERE line_id=? AND user_id=?");

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Update failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "sii", $line_name, $line_id, $user_id);

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

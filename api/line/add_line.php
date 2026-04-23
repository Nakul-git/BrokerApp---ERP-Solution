<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$line_name = trim($_POST['line_name'] ?? '');

if ($line_name === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Description required"
    ]);
    exit;
}

$dup = mysqli_prepare($con, "SELECT line_id FROM line_master WHERE user_id=? AND LOWER(line_name)=LOWER(?) LIMIT 1");
mysqli_stmt_bind_param($dup, "is", $user_id, $line_name);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        "status" => "error",
        "message" => "Line already exists"
    ]);
    exit;
}

$stmt = mysqli_prepare($con, "INSERT INTO line_master (line_name, user_id) VALUES (?, ?)");

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "si", $line_name, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "Line added"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
}
?>

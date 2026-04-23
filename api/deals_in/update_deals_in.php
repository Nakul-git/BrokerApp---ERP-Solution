<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$deals_id = (int)($_POST['deals_id'] ?? 0);
$deals_name = trim($_POST['deals_name'] ?? '');

if ($deals_id <= 0 || $deals_name === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid data"
    ]);
    exit;
}

$dup = mysqli_prepare($con, "SELECT deals_id FROM deals_in_master WHERE user_id=? AND LOWER(deals_name)=LOWER(?) AND deals_id<>? LIMIT 1");
mysqli_stmt_bind_param($dup, "isi", $user_id, $deals_name, $deals_id);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        "status" => "error",
        "message" => "Deals In already exists"
    ]);
    exit;
}

$stmt = mysqli_prepare($con, "UPDATE deals_in_master SET deals_name=? WHERE deals_id=? AND user_id=?");

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Update failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "sii", $deals_name, $deals_id, $user_id);

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

<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$party_category_id = (int)($_POST['party_category_id'] ?? 0);
$party_category_name = trim($_POST['party_category_name'] ?? '');

if ($party_category_id <= 0 || $party_category_name === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid data"
    ]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    "SELECT party_category_id
     FROM party_category_master
     WHERE user_id=? AND LOWER(party_category_name)=LOWER(?) AND party_category_id<>?
     LIMIT 1"
);
mysqli_stmt_bind_param($dup, "isi", $user_id, $party_category_name, $party_category_id);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        "status" => "error",
        "message" => "Party Category already exists"
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    "UPDATE party_category_master
     SET party_category_name=?
     WHERE party_category_id=? AND user_id=?"
);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Update failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "sii", $party_category_name, $party_category_id, $user_id);

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

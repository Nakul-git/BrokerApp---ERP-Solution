<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$party_category_name = trim($_POST['party_category_name'] ?? '');

if ($party_category_name === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Description required"
    ]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    "SELECT party_category_id
     FROM party_category_master
     WHERE user_id=? AND LOWER(party_category_name)=LOWER(?)
     LIMIT 1"
);
mysqli_stmt_bind_param($dup, "is", $user_id, $party_category_name);
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
    "INSERT INTO party_category_master (party_category_name, user_id)
     VALUES (?, ?)"
);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "si", $party_category_name, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "Party Category added"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
}
?>

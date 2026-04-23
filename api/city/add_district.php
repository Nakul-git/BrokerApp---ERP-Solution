<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$user_id = get_master_scope_user_id();

$district_name = trim($_POST['district_name'] ?? '');
$state_id = (int)($_POST['state_id'] ?? 0);

if (!$district_name || !$state_id) {
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit;
}

/* CHECK IF DISTRICT EXISTS */
$check = mysqli_prepare($con,
    "SELECT district_id FROM district WHERE district_name = ? AND user_id = ?"
);

mysqli_stmt_bind_param($check, "si", $district_name, $user_id);
mysqli_stmt_execute($check);
$res = mysqli_stmt_get_result($check);

if ($row = mysqli_fetch_assoc($res)) {
    echo json_encode([
        "status" => "exists",
        "district_id" => $row['district_id']
    ]);
    exit;
}

/* INSERT DISTRICT */
$insert = mysqli_prepare($con,
    "INSERT INTO district (district_name, state_id, user_id)
     VALUES (?, ?, ?)"
);

mysqli_stmt_bind_param($insert, "sii", $district_name, $state_id, $user_id);

if (mysqli_stmt_execute($insert)) {
    echo json_encode([
        "status" => "success",
        "district_id" => mysqli_insert_id($con)
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Insert failed"]);
}
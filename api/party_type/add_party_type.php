<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$party_type_name = trim($_POST['party_type_name'] ?? '');
$party_type_code = strtoupper(trim($_POST['party_type_code'] ?? ''));

if ($party_type_name === '' || $party_type_code === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Description and code required"
    ]);
    exit;
}

$dupName = mysqli_prepare(
    $con,
    "SELECT party_type_id FROM party_type_master WHERE user_id=? AND LOWER(party_type_name)=LOWER(?) LIMIT 1"
);
mysqli_stmt_bind_param($dupName, "is", $user_id, $party_type_name);
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
    "SELECT party_type_id FROM party_type_master WHERE user_id=? AND UPPER(party_type_code)=UPPER(?) LIMIT 1"
);
mysqli_stmt_bind_param($dupCode, "is", $user_id, $party_type_code);
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
    "INSERT INTO party_type_master (party_type_name, party_type_code, user_id)
     VALUES (?, ?, ?)"
);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "ssi", $party_type_name, $party_type_code, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "Party Type added"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
}
?>

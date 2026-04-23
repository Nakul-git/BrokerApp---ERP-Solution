<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$courier_name = trim($_POST['courier_name'] ?? '');
$address1 = trim($_POST['address1'] ?? '');
$address2 = trim($_POST['address2'] ?? '');
$address3 = trim($_POST['address3'] ?? '');
$address4 = trim($_POST['address4'] ?? '');
$station = trim($_POST['station'] ?? '');
$state_name = trim($_POST['state_name'] ?? '');
$pin_code = trim($_POST['pin_code'] ?? '');
$is_active = isset($_POST['is_active']) ? 1 : 0;
$contact_person = trim($_POST['contact_person'] ?? '');
$pan_no = trim($_POST['pan_no'] ?? '');
$applicable_divisions = trim($_POST['applicable_divisions'] ?? '');

if ($courier_name === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Courier name required"
    ]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    "SELECT courier_id FROM courier WHERE user_id=? AND LOWER(courier_name)=LOWER(?) LIMIT 1"
);
mysqli_stmt_bind_param($dup, "is", $user_id, $courier_name);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        "status" => "error",
        "message" => "Courier already exists"
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    "INSERT INTO courier
    (courier_name, address1, address2, address3, address4, station, state_name, pin_code, is_active, contact_person, pan_no, applicable_divisions, user_id)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "ssssssssisssi",
    $courier_name,
    $address1,
    $address2,
    $address3,
    $address4,
    $station,
    $state_name,
    $pin_code,
    $is_active,
    $contact_person,
    $pan_no,
    $applicable_divisions,
    $user_id
);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "Courier added"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
}
?>

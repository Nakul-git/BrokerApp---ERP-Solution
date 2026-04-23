<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$district_id = (int)($_POST['district_id'] ?? 0);
$district_name = trim($_POST['district_name'] ?? '');
$state_id = (int)($_POST['state_id'] ?? 0);
$population = trim($_POST['population'] ?? '');
$area_sq_kms = trim($_POST['area_sq_kms'] ?? '');

if ($district_id <= 0 || $district_name === '' || $state_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid data"
    ]);
    exit;
}

if ($population === '' || !is_numeric($population)) {
    $population = 0;
}

if ($area_sq_kms === '' || !is_numeric($area_sq_kms)) {
    $area_sq_kms = 0;
}

$check_state = mysqli_prepare($con, "SELECT id FROM states WHERE id=? AND user_id=?");
mysqli_stmt_bind_param($check_state, "ii", $state_id, $user_id);
mysqli_stmt_execute($check_state);
$state_res = mysqli_stmt_get_result($check_state);

if (!$state_res || !mysqli_fetch_assoc($state_res)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid state"
    ]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    "SELECT district_id FROM district WHERE user_id=? AND state_id=? AND LOWER(district_name)=LOWER(?) AND district_id<>? LIMIT 1"
);
mysqli_stmt_bind_param($dup, "iisi", $user_id, $state_id, $district_name, $district_id);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        "status" => "error",
        "message" => "District already exists in this state"
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    "UPDATE district
     SET district_name=?, state_id=?, population=?, area_sq_kms=?
     WHERE district_id=? AND user_id=?"
);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Update failed"
    ]);
    exit;
}

$population = (int)$population;
$area_sq_kms = (float)$area_sq_kms;

mysqli_stmt_bind_param($stmt, "siidii", $district_name, $state_id, $population, $area_sq_kms, $district_id, $user_id);

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

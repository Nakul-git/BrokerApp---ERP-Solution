<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$id = (int)($_POST['id'] ?? 0);
$state_name = trim($_POST['state_name'] ?? '');
$state_capital = trim($_POST['state_capital'] ?? '');
$state_area = trim($_POST['state_area'] ?? '');
$state_type = trim($_POST['state_type'] ?? 'INTER-STATE');
$state_code_char = trim($_POST['state_code_char'] ?? '');
$state_code_digit = trim($_POST['state_code_digit'] ?? '');
$user_id = get_master_scope_user_id();

if ($id <= 0 || $state_name === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid data"
    ]);
    exit;
}

/* Prevent duplicate state names per user while updating */
$dup = mysqli_prepare(
    $con,
    "SELECT id FROM states WHERE user_id=? AND LOWER(state_name)=LOWER(?) AND id<>? LIMIT 1"
);
mysqli_stmt_bind_param($dup, "isi", $user_id, $state_name, $id);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        "status" => "error",
        "message" => "State name already exists"
    ]);
    exit;
}

if ($state_area === '' || !is_numeric($state_area)) {
    $state_area = 0;
}

function has_state_column($con, $column_name) {
    $safe = mysqli_real_escape_string($con, $column_name);
    $q = mysqli_query($con, "SHOW COLUMNS FROM states LIKE '{$safe}'");
    return $q && mysqli_num_rows($q) > 0;
}

$set = ["state_name=?"];
$types = "s";
$params = [$state_name];

if (has_state_column($con, "state_capital")) {
    $set[] = "state_capital=?";
    $types .= "s";
    $params[] = $state_capital;
}
if (has_state_column($con, "state_area")) {
    $set[] = "state_area=?";
    $types .= "d";
    $params[] = (float)$state_area;
}
if (has_state_column($con, "state_type")) {
    $set[] = "state_type=?";
    $types .= "s";
    $params[] = $state_type;
}
if (has_state_column($con, "state_code_char")) {
    $set[] = "state_code_char=?";
    $types .= "s";
    $params[] = strtoupper($state_code_char);
}
if (has_state_column($con, "state_code_digit")) {
    $set[] = "state_code_digit=?";
    $types .= "s";
    $params[] = $state_code_digit;
}

$types .= "ii";
$params[] = $id;
$params[] = $user_id;

$sql = "UPDATE states SET " . implode(",", $set) . " WHERE id=? AND user_id=?";
$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Update failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, $types, ...$params);

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

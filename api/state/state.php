<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$state_name = trim($_POST['state_name'] ?? '');
$state_capital = trim($_POST['state_capital'] ?? '');
$state_area = trim($_POST['state_area'] ?? '');
$state_type = trim($_POST['state_type'] ?? 'INTER-STATE');
$state_code_char = trim($_POST['state_code_char'] ?? '');
$state_code_digit = trim($_POST['state_code_digit'] ?? '');
$user_id = get_master_scope_user_id();

if ($state_name === '') {
    echo json_encode([
        "status" => "error",
        "message" => "State name required"
    ]);
    exit;
}

/* Prevent duplicate state names per user (without DB unique constraint) */
$dup = mysqli_prepare(
    $con,
    "SELECT id FROM states WHERE user_id=? AND LOWER(state_name)=LOWER(?) LIMIT 1"
);
mysqli_stmt_bind_param($dup, "is", $user_id, $state_name);
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

$columns = ["state_name", "user_id"];
$types = "si";
$values = [$state_name, $user_id];

if (has_state_column($con, "state_capital")) {
    $columns[] = "state_capital";
    $types .= "s";
    $values[] = $state_capital;
}
if (has_state_column($con, "state_area")) {
    $columns[] = "state_area";
    $types .= "d";
    $values[] = (float)$state_area;
}
if (has_state_column($con, "state_type")) {
    $columns[] = "state_type";
    $types .= "s";
    $values[] = $state_type;
}
if (has_state_column($con, "state_code_char")) {
    $columns[] = "state_code_char";
    $types .= "s";
    $values[] = strtoupper($state_code_char);
}
if (has_state_column($con, "state_code_digit")) {
    $columns[] = "state_code_digit";
    $types .= "s";
    $values[] = $state_code_digit;
}

$placeholders = implode(",", array_fill(0, count($columns), "?"));
$sql = "INSERT INTO states (" . implode(",", $columns) . ") VALUES (" . $placeholders . ")";
$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to prepare insert"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, $types, ...$values);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "State added"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
}
?>

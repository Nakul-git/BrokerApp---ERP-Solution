<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$city_id = (int)($_POST['city_id'] ?? 0);
$city_name = trim($_POST['city_name'] ?? '');
$district_id = isset($_POST['district_id']) && $_POST['district_id'] !== ''
    ? (int)$_POST['district_id']
    : null;
$state_id = (int)($_POST['state_id'] ?? 0);
$pin_code = trim($_POST['pin_code'] ?? '');
$std_code = trim($_POST['std_code'] ?? '');
$party_type = trim($_POST['party_type'] ?? 'INTER-STATE');
$distance_kms = trim($_POST['distance_kms'] ?? '');
$user_id = get_master_scope_user_id();

if ($city_id <= 0 || $city_name === '' || $state_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid data"
    ]);
    exit;
}

if ($distance_kms === '' || !is_numeric($distance_kms)) {
    $distance_kms = 0;
}

function has_city_column($con, $column_name) {
    $safe = mysqli_real_escape_string($con, $column_name);
    $q = mysqli_query($con, "SHOW COLUMNS FROM city LIKE '{$safe}'");
    return $q && mysqli_num_rows($q) > 0;
}

/* ensure selected state belongs to user */
$check_state = mysqli_prepare($con, "SELECT id FROM states WHERE id=? AND user_id=?");
mysqli_stmt_bind_param($check_state, "ii", $state_id, $user_id);
mysqli_stmt_execute($check_state);
$state_result = mysqli_stmt_get_result($check_state);

if (!$state_result || !mysqli_fetch_assoc($state_result)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid state"
    ]);
    exit;
}

/* prevent duplicate city within same state for same user */
$dup = mysqli_prepare(
    $con,
    "SELECT city_id FROM city WHERE user_id=? AND state_id=? AND LOWER(city_name)=LOWER(?) AND city_id<>? LIMIT 1"
);
mysqli_stmt_bind_param($dup, "iisi", $user_id, $state_id, $city_name, $city_id);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        "status" => "error",
        "message" => "City already exists in this state"
    ]);
    exit;
}

$set = ["city_name=?", "state_id=?"];
$types = "si";
$params = [$city_name, $state_id];

if (has_city_column($con, "district_id")) {
    $set[] = "district_id=?";
    if ($district_id === null) {
        $types .= "s";
        $params[] = null;
    } else {
        $types .= "i";
        $params[] = $district_id;
    }
}
if (has_city_column($con, "pin_code")) {
    $set[] = "pin_code=?";
    $types .= "s";
    $params[] = $pin_code;
}
if (has_city_column($con, "std_code")) {
    $set[] = "std_code=?";
    $types .= "s";
    $params[] = $std_code;
}
if (has_city_column($con, "party_type")) {
    $set[] = "party_type=?";
    $types .= "s";
    $params[] = $party_type;
}
if (has_city_column($con, "distance_kms")) {
    $set[] = "distance_kms=?";
    $types .= "d";
    $params[] = (float)$distance_kms;
}

$types .= "ii";
$params[] = $city_id;
$params[] = $user_id;

$sql = "UPDATE city SET " . implode(",", $set) . " WHERE city_id=? AND user_id=?";
$q = mysqli_prepare($con, $sql);

if (!$q) {
    echo json_encode([
        "status" => "error",
        "message" => "Update failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($q, $types, ...$params);

if (mysqli_stmt_execute($q)) {
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

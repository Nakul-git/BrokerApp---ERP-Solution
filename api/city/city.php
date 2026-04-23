<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

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

if ($city_name === '' || $state_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "City name and state are required"
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

/* ==========================================================
   ?? AUTO CREATE DISTRICT IF SAME-AS-DISTRICT IS CHECKED
========================================================== */

$autoCreate = isset($_POST['auto_create_district']);

if ($autoCreate) {

    // Check if district already exists for this user
    $check = mysqli_prepare(
        $con,
        "SELECT district_id FROM district 
         WHERE LOWER(district_name)=LOWER(?) 
         AND user_id=? LIMIT 1"
    );

    mysqli_stmt_bind_param($check, "si", $city_name, $user_id);
    mysqli_stmt_execute($check);
    $res = mysqli_stmt_get_result($check);

    if ($row = mysqli_fetch_assoc($res)) {
        $district_id = (int)$row['district_id'];
    } else {

        $insertDistrict = mysqli_prepare(
            $con,
            "INSERT INTO district (district_name, state_id, user_id)
             VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $insertDistrict,
            "sii",
            $city_name,
            $state_id,
            $user_id
        );

        if (!mysqli_stmt_execute($insertDistrict)) {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to create district"
            ]);
            exit;
        }

        $district_id = mysqli_insert_id($con);
    }
}

/* ==========================================================
   ?? PREVENT DUPLICATE CITY
========================================================== */

$dup = mysqli_prepare(
    $con,
    "SELECT city_id FROM city 
     WHERE user_id=? AND state_id=? 
     AND LOWER(city_name)=LOWER(?) 
     LIMIT 1"
);

mysqli_stmt_bind_param($dup, "iis", $user_id, $state_id, $city_name);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        "status" => "error",
        "message" => "City already exists in this state"
    ]);
    exit;
}

/* ==========================================================
   ?? BUILD INSERT DYNAMICALLY (YOUR EXISTING LOGIC)
========================================================== */

$columns = ["city_name", "state_id", "user_id"];
$types = "sii";
$values = [$city_name, $state_id, $user_id];

if (has_city_column($con, "district_id")) {
    $columns[] = "district_id";

    if ($district_id === null) {
        $types .= "s";
        $values[] = null;
    } else {
        $types .= "i";
        $values[] = $district_id;
    }
}
if (has_city_column($con, "pin_code")) {
    $columns[] = "pin_code";
    $types .= "s";
    $values[] = $pin_code;
}
if (has_city_column($con, "std_code")) {
    $columns[] = "std_code";
    $types .= "s";
    $values[] = $std_code;
}
if (has_city_column($con, "party_type")) {
    $columns[] = "party_type";
    $types .= "s";
    $values[] = $party_type;
}
if (has_city_column($con, "distance_kms")) {
    $columns[] = "distance_kms";
    $types .= "d";
    $values[] = (float)$distance_kms;
}

$placeholders = implode(",", array_fill(0, count($columns), "?"));
$sql = "INSERT INTO city (" . implode(",", $columns) . ") VALUES (" . $placeholders . ")";
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
        "message" => "City added"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
}
?>
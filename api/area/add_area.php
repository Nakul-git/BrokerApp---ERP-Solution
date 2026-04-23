<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$name = trim($_POST['area_name'] ?? '');
$city_id_raw = $_POST['city_id'] ?? '';
$city_id = ($city_id_raw === '' || $city_id_raw === null) ? null : (int)$city_id_raw;

if ($name === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Area name required"
    ]);
    exit;
}

if ($city_id !== null && $city_id > 0) {
    $check_city = mysqli_prepare($con, "SELECT city_id FROM city WHERE city_id=? AND user_id=?");
    mysqli_stmt_bind_param($check_city, "ii", $city_id, $user_id);
    mysqli_stmt_execute($check_city);
    $city_res = mysqli_stmt_get_result($check_city);

    if (!$city_res || !mysqli_fetch_assoc($city_res)) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid city"
        ]);
        exit;
    }
}

/* duplicate check per user + city scope (city can be NULL) */
if ($city_id === null || $city_id === 0) {
    $dup = mysqli_prepare($con, "SELECT area_id FROM area WHERE user_id=? AND city_id IS NULL AND LOWER(name)=LOWER(?) LIMIT 1");
    mysqli_stmt_bind_param($dup, "is", $user_id, $name);
} else {
    $dup = mysqli_prepare($con, "SELECT area_id FROM area WHERE user_id=? AND city_id=? AND LOWER(name)=LOWER(?) LIMIT 1");
    mysqli_stmt_bind_param($dup, "iis", $user_id, $city_id, $name);
}

mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        "status" => "error",
        "message" => "Area already exists"
    ]);
    exit;
}

$stmt = mysqli_prepare($con, "INSERT INTO area (name, city_id, user_id) VALUES (?, ?, ?)");

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "sii", $name, $city_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "Area added"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
}
?>

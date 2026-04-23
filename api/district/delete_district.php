<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$district_id = (int)($_POST['district_id'] ?? 0);

if ($district_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid district"
    ]);
    exit;
}

/* =====================================
   ?? CHECK IF DISTRICT IS USED IN CITY
===================================== */

$check = mysqli_prepare($con,
    "SELECT 1 FROM city 
     WHERE district_id=? AND user_id=? 
     LIMIT 1"
);

mysqli_stmt_bind_param($check, "ii", $district_id, $user_id);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {

    echo json_encode([
        "status" => "error",
        "message" => "Cannot delete. District is in use."
    ]);
    exit;
}

/* =====================================
   ? SAFE TO DELETE
===================================== */

$stmt = mysqli_prepare($con,
    "DELETE FROM district 
     WHERE district_id=? AND user_id=?"
);

mysqli_stmt_bind_param($stmt, "ii", $district_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "District deleted successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Delete failed"
    ]);
}
?>
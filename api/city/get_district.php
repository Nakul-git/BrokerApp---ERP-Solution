<?php
/* =========================================
   STEP 1: JSON HEADER
========================================= */
header("Content-Type: application/json");

/* =========================================
   STEP 2: SESSION + DB
========================================= */
require "../session.php";
require_once '../master_scope.php';   // must define $con and start session

/* =========================================
   STEP 3: VALIDATE SESSION
========================================= */
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized access"
    ]);
    exit;
}

$user_id = get_master_scope_user_id();

/* =========================================
   STEP 4: PREPARE QUERY
========================================= */
$sql = "
    SELECT 
        district_id,
        district_name,
        state_id
    FROM district
    WHERE user_id = ?
    ORDER BY district_name ASC
";

$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Query preparation failed"
    ]);
    exit;
}

/* =========================================
   STEP 5: BIND + EXECUTE
========================================= */
mysqli_stmt_bind_param($stmt, "i", $user_id);

if (!mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "error",
        "message" => "Execution failed"
    ]);
    exit;
}

/* =========================================
   STEP 6: FETCH DATA
========================================= */
$result = mysqli_stmt_get_result($stmt);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

/* =========================================
   STEP 7: RETURN JSON
========================================= */
echo json_encode([
    "status" => "success",
    "data"   => $data
]);

mysqli_stmt_close($stmt);

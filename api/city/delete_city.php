<?php
require "../session.php";
require_once '../master_scope.php';

header("Content-Type: application/json");

$id = $_POST['city_id'] ?? 0;
$user_id = get_master_scope_user_id();

if (!$id) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid city ID"
    ]);
    exit;
}

/* =====================================
   🔍 CHECK DEPENDENCIES FIRST
===================================== */

/* Example dependency checks
   Replace these with YOUR real tables
*/

$dependencyQueries = [

    // Example: customers table
    "SELECT 1 FROM customers WHERE city_id=? AND user_id=? LIMIT 1",

    // Example: suppliers table
    "SELECT 1 FROM suppliers WHERE city_id=? AND user_id=? LIMIT 1",

    // Example: branches table
    "SELECT 1 FROM branches WHERE city_id=? AND user_id=? LIMIT 1"

];

foreach ($dependencyQueries as $sql) {

    $check = mysqli_prepare($con, $sql);
    if (!$check) {
        continue;
    }
    mysqli_stmt_bind_param($check, "ii", $id, $user_id);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {

        echo json_encode([
            "status" => "error",
            "message" => "Cannot delete. City is in use."
        ]);
        exit;
    }
}

/* =====================================
   ✅ SAFE TO DELETE
===================================== */

$q = mysqli_prepare($con,
    "DELETE FROM city
     WHERE city_id=? AND user_id=?"
);

mysqli_stmt_bind_param($q, "ii", $id, $user_id);

if (mysqli_stmt_execute($q)) {
    echo json_encode([
        "status" => "success",
        "message" => "City deleted successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Delete failed"
    ]);
}

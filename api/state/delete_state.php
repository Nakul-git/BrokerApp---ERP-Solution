<?php
require "../session.php";
require_once '../master_scope.php';

header("Content-Type: application/json");

/* ===============================
   VALIDATION
================================ */

if (!isset($_POST['id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request"
    ]);
    exit;
}

$id = intval($_POST['id']);
$user_id = get_master_scope_user_id();

if ($id <= 0 || $user_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid parameters"
    ]);
    exit;
}

/* ===============================
   CHECK CITY DEPENDENCY
================================ */

$cityCheck = mysqli_prepare($con,
    "SELECT COUNT(*) 
     FROM city 
     WHERE state_id = ? AND user_id = ?"
);

mysqli_stmt_bind_param($cityCheck, "ii", $id, $user_id);
mysqli_stmt_execute($cityCheck);
mysqli_stmt_bind_result($cityCheck, $cityCount);
mysqli_stmt_fetch($cityCheck);
mysqli_stmt_close($cityCheck);

if ($cityCount > 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Cannot delete. State is linked to one or more cities."
    ]);
    exit;
}

/* ===============================
   CHECK DISTRICT DEPENDENCY
================================ */

$districtCheck = mysqli_prepare($con,
    "SELECT COUNT(*) 
     FROM district 
     WHERE state_id = ? AND user_id = ?"
);

mysqli_stmt_bind_param($districtCheck, "ii", $id, $user_id);
mysqli_stmt_execute($districtCheck);
mysqli_stmt_bind_result($districtCheck, $districtCount);
mysqli_stmt_fetch($districtCheck);
mysqli_stmt_close($districtCheck);

if ($districtCount > 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Cannot delete. State is linked to one or more districts."
    ]);
    exit;
}

/* ===============================
   SAFE DELETE
================================ */

$delete = mysqli_prepare($con,
    "DELETE FROM states 
     WHERE id = ? AND user_id = ?"
);

mysqli_stmt_bind_param($delete, "ii", $id, $user_id);

if (mysqli_stmt_execute($delete)) {

    if (mysqli_stmt_affected_rows($delete) > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "State deleted successfully"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "State not found or already deleted"
        ]);
    }

} else {
    echo json_encode([
        "status" => "error",
        "message" => "Delete failed"
    ]);
}

mysqli_stmt_close($delete);
?>

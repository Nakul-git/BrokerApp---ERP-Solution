<?php
header("Content-Type: application/json");
require "../session.php";
require_once "../master_scope.php";

$user_id = get_master_scope_user_id();
$division_id = (int)($_POST['division_id'] ?? 0);

if ($division_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid division"
    ]);
    exit;
}


/* ✅ use company DB */

$stmt = mysqli_prepare(
    $con_company,
    "DELETE FROM division_master
     WHERE division_id=? AND user_id=?"
);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $division_id,
    $user_id
);

if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "status" => "success",
        "message" => "Deleted"
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Delete failed"
    ]);
}
?>

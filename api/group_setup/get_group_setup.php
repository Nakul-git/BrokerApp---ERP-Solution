<?php
header("Content-Type: application/json");
require "../session.php";
require_once "../master_scope.php";

$user_id = get_master_scope_user_id();

$stmt = mysqli_prepare($con, "SELECT group_fix_id, allowed_group_ids_json FROM group_setup WHERE user_id=?");
if (!$stmt) {
    echo json_encode([ "status" => "error", "message" => "Prepare failed", "error" => mysqli_error($con) ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$res = mysqli_stmt_get_result($stmt);
$rows = [];
while ($row = mysqli_fetch_assoc($res)) {
    $rows[] = $row;
}

echo json_encode($rows);
?>

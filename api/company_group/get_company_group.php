<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();

$stmt = mysqli_prepare(
    $con,
    "SELECT company_group_id, acc_name, address1, address2, address3, address4, station, state_name, pin_code, is_active, phone_no, contact_person, pan_no, applicable_divisions
     FROM company_group
     WHERE user_id=?
     ORDER BY acc_name"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$data = [];
while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}

echo json_encode($data);
?>
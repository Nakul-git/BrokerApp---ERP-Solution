<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();

$stmt = mysqli_prepare(
    $con,
    "SELECT group_id, sort_order, main_group_name, group_name, group_type, maintain_bill_outstanding, suppress_trial_balance, address_details_req, general_ledger, group_primary
     FROM group_master
     WHERE user_id=?
     ORDER BY sort_order, group_name"
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
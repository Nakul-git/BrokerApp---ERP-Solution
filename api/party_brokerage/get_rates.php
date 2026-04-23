<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$party_id = (int)($_POST['party_id'] ?? 0);

if ($party_id <= 0) {
    echo json_encode([]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    "SELECT rate_id, party_id, product_id, slr_type, slr_rt, byr_type, byr_rt
     FROM party_brokerage_rate
     WHERE user_id=? AND party_id=?
     ORDER BY product_id"
);
mysqli_stmt_bind_param($stmt, "ii", $user_id, $party_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$data = [];
while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}

echo json_encode($data);
?>
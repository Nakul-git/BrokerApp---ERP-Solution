<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$party_id = (int)($_POST['party_id'] ?? 0);

function has_table($con, $table) {
    $table_safe = mysqli_real_escape_string($con, $table);
    $q = mysqli_query($con, "SHOW TABLES LIKE '{$table_safe}'");
    return $q && mysqli_num_rows($q) > 0;
}

if ($party_id <= 0) {
    echo json_encode([]);
    exit;
}

if (!has_table($con, 'party_brokerage_packing_rate')) {
    echo json_encode([]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    "SELECT pack_rate_id, party_id, packing, slr_rt, byr_rt
     FROM party_brokerage_packing_rate
     WHERE user_id=? AND party_id=?
     ORDER BY pack_rate_id"
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

<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../master_scope.php';

$userId = get_master_scope_user_id();
$rows = [];

$stmt = mysqli_prepare(
    $con,
    "SELECT party_id, party_name, city, state, area,
            party_role_byr, party_role_slr, party_role_sb, party_role_bb
     FROM party
     WHERE user_id = ?
     ORDER BY party_name"
);
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($res && ($row = mysqli_fetch_assoc($res))) {
    $rows[] = $row;
}
mysqli_stmt_close($stmt);

echo json_encode([
    'status' => 'success',
    'rows' => $rows
]);
?>

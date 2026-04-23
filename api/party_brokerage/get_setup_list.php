<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();

function has_table($con, $table) {
    $table_safe = mysqli_real_escape_string($con, $table);
    $q = mysqli_query($con, "SHOW TABLES LIKE '{$table_safe}'");
    return $q && mysqli_num_rows($q) > 0;
}

$has_packing = has_table($con, 'party_brokerage_packing_rate');

if ($has_packing) {
    $sql = "SELECT p.party_id, p.party_name, p.city, COALESCE(pr.item_count, 0) AS item_count
            FROM (
                SELECT party_id FROM party_brokerage_rate WHERE user_id=?
                UNION
                SELECT party_id FROM party_brokerage_packing_rate WHERE user_id=?
            ) u
            INNER JOIN party p ON p.party_id = u.party_id AND p.user_id = ?
            LEFT JOIN (
                SELECT party_id, COUNT(rate_id) AS item_count
                FROM party_brokerage_rate
                WHERE user_id=?
                GROUP BY party_id
            ) pr ON pr.party_id = p.party_id
            ORDER BY p.party_name";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iiii", $user_id, $user_id, $user_id, $user_id);
} else {
    $sql = "SELECT p.party_id, p.party_name, p.city, COUNT(r.rate_id) AS item_count
            FROM party_brokerage_rate r
            INNER JOIN party p ON p.party_id = r.party_id AND p.user_id = r.user_id
            WHERE r.user_id = ?
            GROUP BY p.party_id, p.party_name, p.city
            ORDER BY p.party_name";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
}

mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$data = [];
while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}

echo json_encode($data);
?>

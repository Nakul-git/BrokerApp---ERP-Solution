<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

function table_exists($con, $table) {
    $safe = mysqli_real_escape_string($con, $table);
    $q = mysqli_query($con, "SHOW TABLES LIKE '{$safe}'");
    return $q && mysqli_num_rows($q) > 0;
}

$party_id = (int)($_POST['party_id'] ?? 0);
$user_id = get_master_scope_user_id();

if ($party_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid party"]);
    exit;
}

mysqli_begin_transaction($con);
try {
    if (table_exists($con, 'party_bank_detail')) mysqli_query($con, "DELETE FROM party_bank_detail WHERE party_id=" . (int)$party_id . " AND user_id=" . (int)$user_id);
    if (table_exists($con, 'party_division_balance')) mysqli_query($con, "DELETE FROM party_division_balance WHERE party_id=" . (int)$party_id . " AND user_id=" . (int)$user_id);
    if (table_exists($con, 'party_condition_map')) mysqli_query($con, "DELETE FROM party_condition_map WHERE party_id=" . (int)$party_id . " AND user_id=" . (int)$user_id);
    if (table_exists($con, 'party_product_brand_setup')) mysqli_query($con, "DELETE FROM party_product_brand_setup WHERE party_id=" . (int)$party_id . " AND user_id=" . (int)$user_id);
    if (table_exists($con, 'party_deals_map')) mysqli_query($con, "DELETE FROM party_deals_map WHERE party_id=" . (int)$party_id . " AND user_id=" . (int)$user_id);

    $q = mysqli_prepare($con, "DELETE FROM party WHERE party_id=? AND user_id=?");
    mysqli_stmt_bind_param($q, "ii", $party_id, $user_id);
    if (!mysqli_stmt_execute($q)) throw new Exception('Delete failed');

    mysqli_commit($con);
    echo json_encode(["status" => "success", "message" => "Deleted"]);
} catch (Throwable $e) {
    mysqli_rollback($con);
    echo json_encode(["status" => "error", "message" => "Delete failed: " . $e->getMessage()]);
}
?>

<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

function table_exists($con, $table) {
    $safe = mysqli_real_escape_string($con, $table);
    $q = mysqli_query($con, "SHOW TABLES LIKE '{$safe}'");
    return $q && mysqli_num_rows($q) > 0;
}

function column_exists($con, $table, $column) {
    $safe_table = mysqli_real_escape_string($con, $table);
    $safe_col = mysqli_real_escape_string($con, $column);
    $q = mysqli_query($con, "SHOW COLUMNS FROM `{$safe_table}` LIKE '{$safe_col}'");
    return $q && mysqli_num_rows($q) > 0;
}

$user_id = get_master_scope_user_id();
$party_id = (int)($_POST['party_id'] ?? 0);

if ($party_id <= 0) {
    echo json_encode([
        "bank_rows" => [],
        "division_balances" => [],
        "payment_condition_ids" => [],
        "packing_condition_ids" => [],
        "seller_rows" => [],
        "buyer_rows" => [],
        "deals_ids" => []
    ]);
    exit;
}

$out = [
    "bank_rows" => [],
    "division_balances" => [],
    "payment_condition_ids" => [],
    "packing_condition_ids" => [],
    "seller_rows" => [],
    "buyer_rows" => [],
    "deals_ids" => []
];

if (table_exists($con, 'party_bank_detail')) {
    $q = mysqli_prepare($con, "SELECT ac_holder, ac_number, bank_name, ifsc_code, pin_code FROM party_bank_detail WHERE party_id=? AND user_id=? ORDER BY row_no, bank_id");
    mysqli_stmt_bind_param($q, "ii", $party_id, $user_id);
    mysqli_stmt_execute($q);
    $res = mysqli_stmt_get_result($q);
    while ($r = mysqli_fetch_assoc($res)) $out["bank_rows"][] = $r;
}

if (table_exists($con, 'party_division_balance')) {
    $q = mysqli_prepare($con, "SELECT division_id, opening_balance, dc, hb_opening_balance, hb_dc FROM party_division_balance WHERE party_id=? AND user_id=? ORDER BY row_no, division_balance_id");
    mysqli_stmt_bind_param($q, "ii", $party_id, $user_id);
    mysqli_stmt_execute($q);
    $res = mysqli_stmt_get_result($q);
    while ($r = mysqli_fetch_assoc($res)) $out["division_balances"][] = $r;
}

if (table_exists($con, 'party_condition_map')) {
    $q = mysqli_prepare($con, "SELECT side_type, condition_id FROM party_condition_map WHERE party_id=? AND user_id=?");
    mysqli_stmt_bind_param($q, "ii", $party_id, $user_id);
    mysqli_stmt_execute($q);
    $res = mysqli_stmt_get_result($q);
    while ($r = mysqli_fetch_assoc($res)) {
        if (($r['side_type'] ?? '') === 'PAYMENT') $out["payment_condition_ids"][] = (int)$r['condition_id'];
        if (($r['side_type'] ?? '') === 'PACKING') $out["packing_condition_ids"][] = (int)$r['condition_id'];
    }
}

if (table_exists($con, 'party_product_brand_setup')) {
    $q = mysqli_prepare($con, "SELECT side_type, product_id, brand_id, pack FROM party_product_brand_setup WHERE party_id=? AND user_id=? ORDER BY side_type, row_no, row_id");
    mysqli_stmt_bind_param($q, "ii", $party_id, $user_id);
    mysqli_stmt_execute($q);
    $res = mysqli_stmt_get_result($q);
    while ($r = mysqli_fetch_assoc($res)) {
        $row = [
            "product_id" => (int)($r['product_id'] ?? 0),
            "brand_id" => (int)($r['brand_id'] ?? 0),
            "pack" => (string)($r['pack'] ?? '')
        ];
        if (($r['side_type'] ?? '') === 'SELLER') $out["seller_rows"][] = $row;
        if (($r['side_type'] ?? '') === 'BUYER') $out["buyer_rows"][] = $row;
    }
}

if (column_exists($con, 'party', 'deals_ids')) {
    $q = mysqli_prepare($con, "SELECT deals_ids FROM party WHERE party_id=? AND user_id=? LIMIT 1");
    mysqli_stmt_bind_param($q, "ii", $party_id, $user_id);
    mysqli_stmt_execute($q);
    $res = mysqli_stmt_get_result($q);
    if ($res && ($r = mysqli_fetch_assoc($res))) {
        $csv = (string)($r['deals_ids'] ?? '');
        if ($csv !== '') {
            $out["deals_ids"] = array_values(array_unique(array_filter(array_map('intval', explode(',', $csv)))));
        }
    }
} else if (table_exists($con, 'party_deals_map')) {
    $q = mysqli_prepare($con, "SELECT narration_id FROM party_deals_map WHERE party_id=? AND user_id=? ORDER BY map_id");
    mysqli_stmt_bind_param($q, "ii", $party_id, $user_id);
    mysqli_stmt_execute($q);
    $res = mysqli_stmt_get_result($q);
    while ($r = mysqli_fetch_assoc($res)) $out["deals_ids"][] = (int)$r['narration_id'];
}

echo json_encode($out);
?>

<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

function table_exists($con, $table) {
    $safe = mysqli_real_escape_string($con, $table);
    $q = mysqli_query($con, "SHOW TABLES LIKE '{$safe}'");
    return $q && mysqli_num_rows($q) > 0;
}

function get_table_columns($con, $table) {
    $cols = [];
    $safe = mysqli_real_escape_string($con, $table);
    $q = mysqli_query($con, "SHOW COLUMNS FROM `{$safe}`");
    if (!$q) return $cols;
    while ($r = mysqli_fetch_assoc($q)) {
        $cols[$r['Field']] = true;
    }
    return $cols;
}

$user_id = get_master_scope_user_id();
$product_cols = get_table_columns($con, 'product');

$base_cols = ['product_id', 'product_name', 'sales_rate', 'rate'];
$optional_cols = [
    'item_code','material_type','product_group','default_item','std_pack','pack_unit','rate_type','div_factor',
    'rate_range_from','rate_range_to','qty_range_from','qty_range_to','weight_range_from','weight_range_to',
    'cursor_brand','cursor_weight','cursor_unit','cursor_qty','cursor_pack','cursor_sp_pack','kasar_rate',
    'term_type_flag','amt_ro','edit_flag','brok_byr_type','brok_byr_rate','brok_slr_type','brok_slr_rate',
    'packing_compulsory','link_with_master','igst','cgst','sgst','ord_no','tax_pack_max','remarks',
    'def_loading_pend','freight_type'
];

$select_cols = [];
foreach ($base_cols as $c) {
    if (isset($product_cols[$c])) $select_cols[] = 'p.' . $c;
}
foreach ($optional_cols as $c) {
    if (isset($product_cols[$c])) $select_cols[] = 'p.' . $c;
}

if (!count($select_cols)) {
    echo json_encode([]);
    exit;
}

$has_product_brand = table_exists($con, 'product_brand');
$sql = 'SELECT ' . implode(', ', $select_cols);
if ($has_product_brand) {
    $sql .= ', GROUP_CONCAT(pb.brand_id ORDER BY pb.brand_id) AS brand_ids';
}
$sql .= ' FROM product p ';
if ($has_product_brand) {
    $sql .= ' LEFT JOIN product_brand pb ON pb.product_id = p.product_id AND pb.user_id = p.user_id ';
}
$sql .= ' WHERE p.user_id=? ';
if ($has_product_brand) {
    $sql .= ' GROUP BY ' . implode(', ', $select_cols);
}
$sql .= ' ORDER BY p.product_name';

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$data = [];
$product_ids = [];
while ($row = mysqli_fetch_assoc($res)) {
    $ids = [];
    if (!empty($row['brand_ids'])) {
        $ids = array_values(array_filter(array_map('intval', explode(',', $row['brand_ids'])), function ($v) { return $v > 0; }));
    }
    $row['brand_ids'] = $ids;
    $row['packing_rows'] = [];
    $data[] = $row;
    if (!empty($row['product_id'])) $product_ids[] = (int)$row['product_id'];
}

if (count($product_ids) && table_exists($con, 'product_packing_detail')) {
    $in = implode(',', array_map('intval', array_values(array_unique($product_ids))));
    $pack_q = mysqli_query(
        $con,
        "SELECT product_id, row_no, packing, byr_rt, slr_rt
         FROM product_packing_detail
         WHERE user_id=" . (int)$user_id . " AND product_id IN (" . $in . ")
         ORDER BY product_id, row_no, pack_id"
    );

    $pack_map = [];
    if ($pack_q) {
        while ($pr = mysqli_fetch_assoc($pack_q)) {
            $pid = (int)$pr['product_id'];
            if (!isset($pack_map[$pid])) $pack_map[$pid] = [];
            $pack_map[$pid][] = [
                'packing' => (string)($pr['packing'] ?? ''),
                'byr_rt' => $pr['byr_rt'] ?? 0,
                'slr_rt' => $pr['slr_rt'] ?? 0
            ];
        }
    }

    foreach ($data as &$d) {
        $pid = (int)($d['product_id'] ?? 0);
        $d['packing_rows'] = $pack_map[$pid] ?? [];
    }
    unset($d);
}

echo json_encode($data);
?>

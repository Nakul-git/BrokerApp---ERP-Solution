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

function post_str($key, $default = '') { return trim((string)($_POST[$key] ?? $default)); }
function post_num($key, $default = 0) { return (float)($_POST[$key] ?? $default); }
function post_int($key, $default = 0) { return (int)($_POST[$key] ?? $default); }
function post_bool($key) { return isset($_POST[$key]) ? 1 : 0; }
function sql_val($con, $value, $type) {
    if ($type === 'int') return (string)((int)$value);
    if ($type === 'num') return (string)((float)$value);
    return "'" . mysqli_real_escape_string($con, (string)$value) . "'";
}

$user_id = get_master_scope_user_id();
$product_id = post_int('product_id', 0);
$product_name = post_str('product_name');
$sales_rate = post_num('sales_rate', 0);
$rate = post_num('rate', 0);
$brands_json = $_POST['brands_json'] ?? '[]';
$packing_json = $_POST['packing_json'] ?? '[]';

$brand_ids = json_decode($brands_json, true);
if (!is_array($brand_ids)) $brand_ids = [];
$brand_ids = array_values(array_unique(array_map('intval', $brand_ids)));
$brand_ids = array_values(array_filter($brand_ids, function ($v) { return $v > 0; }));

$packing_rows = json_decode($packing_json, true);
if (!is_array($packing_rows)) $packing_rows = [];

if ($product_id <= 0 || $product_name === '') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}

$dup = mysqli_prepare($con, 'SELECT product_id FROM product WHERE user_id=? AND LOWER(product_name)=LOWER(?) AND product_id<>? LIMIT 1');
mysqli_stmt_bind_param($dup, 'isi', $user_id, $product_name, $product_id);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);
if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode(['status' => 'error', 'message' => 'Product already exists']);
    exit;
}

$product_cols = get_table_columns($con, 'product');
$set = [
    'product_name=' . sql_val($con, $product_name, 'str'),
    'sales_rate=' . sql_val($con, $sales_rate, 'num'),
    'rate=' . sql_val($con, $rate, 'num')
];

$extra = [
    'item_code' => ['value' => post_str('item_code'), 'type' => 'str'],
    'material_type' => ['value' => post_str('material_type', 'FINISHED GOODS'), 'type' => 'str'],
    'product_group' => ['value' => post_str('product_group'), 'type' => 'str'],
    'default_item' => ['value' => post_bool('default_item'), 'type' => 'int'],
    'std_pack' => ['value' => post_num('std_pack', 0), 'type' => 'num'],
    'pack_unit' => ['value' => post_str('pack_unit'), 'type' => 'str'],
    'rate_type' => ['value' => post_str('rate_type', 'W'), 'type' => 'str'],
    'div_factor' => ['value' => post_num('div_factor', 1), 'type' => 'num'],
    'rate_range_from' => ['value' => post_num('rate_range_from', 0), 'type' => 'num'],
    'rate_range_to' => ['value' => post_num('rate_range_to', 0), 'type' => 'num'],
    'qty_range_from' => ['value' => post_num('qty_range_from', 0), 'type' => 'num'],
    'qty_range_to' => ['value' => post_num('qty_range_to', 0), 'type' => 'num'],
    'weight_range_from' => ['value' => post_num('weight_range_from', 0), 'type' => 'num'],
    'weight_range_to' => ['value' => post_num('weight_range_to', 0), 'type' => 'num'],
    'cursor_brand' => ['value' => post_bool('cursor_brand'), 'type' => 'int'],
    'cursor_weight' => ['value' => post_bool('cursor_weight'), 'type' => 'int'],
    'cursor_unit' => ['value' => post_bool('cursor_unit'), 'type' => 'int'],
    'cursor_qty' => ['value' => post_bool('cursor_qty'), 'type' => 'int'],
    'cursor_pack' => ['value' => post_bool('cursor_pack'), 'type' => 'int'],
    'cursor_sp_pack' => ['value' => post_bool('cursor_sp_pack'), 'type' => 'int'],
    'kasar_rate' => ['value' => post_bool('kasar_rate'), 'type' => 'int'],
    'term_type_flag' => ['value' => post_bool('term_type_flag'), 'type' => 'int'],
    'amt_ro' => ['value' => post_str('amt_ro', 'N'), 'type' => 'str'],
    'edit_flag' => ['value' => post_str('edit_flag', 'Y'), 'type' => 'str'],
    'brok_byr_type' => ['value' => post_str('brok_byr_type', 'PERCENT'), 'type' => 'str'],
    'brok_byr_rate' => ['value' => post_num('brok_byr_rate', 0), 'type' => 'num'],
    'brok_slr_type' => ['value' => post_str('brok_slr_type', 'PERCENT'), 'type' => 'str'],
    'brok_slr_rate' => ['value' => post_num('brok_slr_rate', 0), 'type' => 'num'],
    'packing_compulsory' => ['value' => post_bool('packing_compulsory'), 'type' => 'int'],
    'link_with_master' => ['value' => post_bool('link_with_master'), 'type' => 'int'],
    'igst' => ['value' => post_num('igst', 0), 'type' => 'num'],
    'cgst' => ['value' => post_num('cgst', 0), 'type' => 'num'],
    'sgst' => ['value' => post_num('sgst', 0), 'type' => 'num'],
    'ord_no' => ['value' => post_int('ord_no', 0), 'type' => 'int'],
    'tax_pack_max' => ['value' => post_num('tax_pack_max', 25), 'type' => 'num'],
    'remarks' => ['value' => post_str('remarks'), 'type' => 'str'],
    'def_loading_pend' => ['value' => post_str('def_loading_pend', 'W'), 'type' => 'str'],
    'freight_type' => ['value' => post_str('freight_type', 'W'), 'type' => 'str']
];

foreach ($extra as $col => $meta) {
    if (!isset($product_cols[$col])) continue;
    $set[] = $col . '=' . sql_val($con, $meta['value'], $meta['type']);
}

mysqli_begin_transaction($con);

try {
    $sql = 'UPDATE product SET ' . implode(', ', $set)
        . ' WHERE product_id=' . (int)$product_id . ' AND user_id=' . (int)$user_id;
    if (!mysqli_query($con, $sql)) {
        throw new Exception('Update failed');
    }

    if (table_exists($con, 'product_brand')) {
        $del_map = mysqli_prepare($con, 'DELETE FROM product_brand WHERE product_id=? AND user_id=?');
        mysqli_stmt_bind_param($del_map, 'ii', $product_id, $user_id);
        if (!mysqli_stmt_execute($del_map)) {
            throw new Exception('Brand map clear failed');
        }

        if (!empty($brand_ids)) {
            $check_brand = mysqli_prepare($con, 'SELECT brand_id FROM brand WHERE brand_id=? AND user_id=? LIMIT 1');
            $ins_brand = mysqli_prepare($con, 'INSERT INTO product_brand (product_id, brand_id, user_id) VALUES (?, ?, ?)');
            if (!$check_brand || !$ins_brand) {
                throw new Exception('Brand mapping prepare failed');
            }
            foreach ($brand_ids as $brand_id) {
                mysqli_stmt_bind_param($check_brand, 'ii', $brand_id, $user_id);
                mysqli_stmt_execute($check_brand);
                $brand_res = mysqli_stmt_get_result($check_brand);
                if (!$brand_res || !mysqli_fetch_assoc($brand_res)) {
                    throw new Exception('Invalid brand selected');
                }
                mysqli_stmt_bind_param($ins_brand, 'iii', $product_id, $brand_id, $user_id);
                if (!mysqli_stmt_execute($ins_brand)) {
                    throw new Exception('Brand map insert failed');
                }
            }
        }
    }

    if (table_exists($con, 'product_packing_detail')) {
        $del_pack = mysqli_prepare($con, 'DELETE FROM product_packing_detail WHERE product_id=? AND user_id=?');
        mysqli_stmt_bind_param($del_pack, 'ii', $product_id, $user_id);
        if (!mysqli_stmt_execute($del_pack)) {
            throw new Exception('Packing clear failed');
        }

        if (!empty($packing_rows)) {
            $ins_pack = mysqli_prepare($con, 'INSERT INTO product_packing_detail (product_id, row_no, packing, byr_rt, slr_rt, user_id) VALUES (?, ?, ?, ?, ?, ?)');
            if (!$ins_pack) throw new Exception('Packing prepare failed');

            $row_no = 1;
            foreach ($packing_rows as $r) {
                $packing = trim((string)($r['packing'] ?? ''));
                $byr_rt = (float)($r['byr_rt'] ?? 0);
                $slr_rt = (float)($r['slr_rt'] ?? 0);
                if ($packing === '' && $byr_rt == 0.0 && $slr_rt == 0.0) continue;
                mysqli_stmt_bind_param($ins_pack, 'iisddi', $product_id, $row_no, $packing, $byr_rt, $slr_rt, $user_id);
                if (!mysqli_stmt_execute($ins_pack)) {
                    throw new Exception('Packing insert failed');
                }
                $row_no += 1;
            }
        }
    }

    mysqli_commit($con);
    echo json_encode(['status' => 'success', 'message' => 'Updated']);
} catch (Throwable $e) {
    mysqli_rollback($con);
    echo json_encode(['status' => 'error', 'message' => 'Update failed: ' . $e->getMessage()]);
}
?>

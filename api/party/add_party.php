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
    while ($r = mysqli_fetch_assoc($q)) $cols[$r['Field']] = true;
    return $cols;
}

function post_str($k, $d = '') { return trim((string)($_POST[$k] ?? $d)); }
function post_num($k, $d = 0) { return (float)($_POST[$k] ?? $d); }
function post_int($k, $d = 0) { return (int)($_POST[$k] ?? $d); }
function post_bool($k) { return isset($_POST[$k]) ? 1 : 0; }
function sql_val($con, $value, $type) {
    if ($type === 'int') return (string)((int)$value);
    if ($type === 'num') return (string)((float)$value);
    return "'" . mysqli_real_escape_string($con, (string)$value) . "'";
}

function save_related($con, $party_id, $user_id) {
    $bank_rows = json_decode($_POST['bank_rows_json'] ?? '[]', true);
    $division_rows = json_decode($_POST['division_balances_json'] ?? '[]', true);
    $seller_rows = json_decode($_POST['seller_rows_json'] ?? '[]', true);
    $buyer_rows = json_decode($_POST['buyer_rows_json'] ?? '[]', true);
    $payment_ids = json_decode($_POST['payment_condition_ids_json'] ?? '[]', true);
    $packing_ids = json_decode($_POST['packing_condition_ids_json'] ?? '[]', true);

    if (!is_array($bank_rows)) $bank_rows = [];
    if (!is_array($division_rows)) $division_rows = [];
    if (!is_array($seller_rows)) $seller_rows = [];
    if (!is_array($buyer_rows)) $buyer_rows = [];
    if (!is_array($payment_ids)) $payment_ids = [];
    if (!is_array($packing_ids)) $packing_ids = [];

    if (table_exists($con, 'party_bank_detail')) {
        $ins = mysqli_prepare($con, "INSERT INTO party_bank_detail (party_id, row_no, ac_holder, ac_number, bank_name, ifsc_code, pin_code, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $row_no = 1;
        foreach ($bank_rows as $r) {
            $ac_holder = trim((string)($r['ac_holder'] ?? ''));
            $ac_number = trim((string)($r['ac_number'] ?? ''));
            $bank_name = trim((string)($r['bank_name'] ?? ''));
            $ifsc_code = trim((string)($r['ifsc_code'] ?? ''));
            $pin_code = trim((string)($r['pin_code'] ?? ''));
            if ($ac_holder === '' && $ac_number === '' && $bank_name === '' && $ifsc_code === '' && $pin_code === '') continue;
            mysqli_stmt_bind_param($ins, "iisssssi", $party_id, $row_no, $ac_holder, $ac_number, $bank_name, $ifsc_code, $pin_code, $user_id);
            if (!mysqli_stmt_execute($ins)) throw new Exception('Failed saving bank rows');
            $row_no += 1;
        }
    }

    if (table_exists($con, 'party_division_balance')) {
        $ins = mysqli_prepare($con, "INSERT INTO party_division_balance (party_id, row_no, division_id, opening_balance, dc, hb_opening_balance, hb_dc, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $row_no = 1;
        foreach ($division_rows as $r) {
            $division_id = (int)($r['division_id'] ?? 0);
            if ($division_id <= 0) continue;
            $opening = (float)($r['opening_balance'] ?? 0);
            $dc = strtoupper(trim((string)($r['dc'] ?? 'DB')));
            if ($dc !== 'CR') $dc = 'DB';
            $hb_opening = (float)($r['hb_opening_balance'] ?? 0);
            $hb_dc = strtoupper(trim((string)($r['hb_dc'] ?? 'DB')));
            if ($hb_dc !== 'CR') $hb_dc = 'DB';
            mysqli_stmt_bind_param($ins, "iiidsdsi", $party_id, $row_no, $division_id, $opening, $dc, $hb_opening, $hb_dc, $user_id);
            if (!mysqli_stmt_execute($ins)) throw new Exception('Failed saving division rows');
            $row_no += 1;
        }
    }

    if (table_exists($con, 'party_condition_map')) {
        $ins = mysqli_prepare($con, "INSERT INTO party_condition_map (party_id, side_type, condition_id, user_id) VALUES (?, ?, ?, ?)");
        $payment_ids = array_values(array_unique(array_filter(array_map('intval', $payment_ids))));
        $packing_ids = array_values(array_unique(array_filter(array_map('intval', $packing_ids))));
        foreach ($payment_ids as $cid) {
            $side = 'PAYMENT';
            mysqli_stmt_bind_param($ins, "isii", $party_id, $side, $cid, $user_id);
            if (!mysqli_stmt_execute($ins)) throw new Exception('Failed saving payment conditions');
        }
        foreach ($packing_ids as $cid) {
            $side = 'PACKING';
            mysqli_stmt_bind_param($ins, "isii", $party_id, $side, $cid, $user_id);
            if (!mysqli_stmt_execute($ins)) throw new Exception('Failed saving packing conditions');
        }
    }

    if (table_exists($con, 'party_product_brand_setup')) {
        $ins = mysqli_prepare($con, "INSERT INTO party_product_brand_setup (party_id, side_type, row_no, product_id, brand_id, pack, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $row_no = 1;
        foreach ($seller_rows as $r) {
            $pid = (int)($r['product_id'] ?? 0);
            $bid = (int)($r['brand_id'] ?? 0);
            $pack = trim((string)($r['pack'] ?? ''));
            if ($pid <= 0 && $bid <= 0 && $pack === '') continue;
            $side = 'SELLER';
            mysqli_stmt_bind_param($ins, "isiiisi", $party_id, $side, $row_no, $pid, $bid, $pack, $user_id);
            if (!mysqli_stmt_execute($ins)) throw new Exception('Failed saving seller setup');
            $row_no += 1;
        }
        $row_no = 1;
        foreach ($buyer_rows as $r) {
            $pid = (int)($r['product_id'] ?? 0);
            $bid = (int)($r['brand_id'] ?? 0);
            $pack = trim((string)($r['pack'] ?? ''));
            if ($pid <= 0 && $bid <= 0 && $pack === '') continue;
            $side = 'BUYER';
            mysqli_stmt_bind_param($ins, "isiiisi", $party_id, $side, $row_no, $pid, $bid, $pack, $user_id);
            if (!mysqli_stmt_execute($ins)) throw new Exception('Failed saving buyer setup');
            $row_no += 1;
        }
    }

}

$user_id = get_master_scope_user_id();
$party_name = post_str('party_name');
$city = post_str('city');

if ($party_name === '' || $city === '') {
    echo json_encode(["status" => "error", "message" => "Party name and city are required"]);
    exit;
}

$dup = mysqli_prepare($con, "SELECT party_id FROM party WHERE user_id=? AND LOWER(party_name)=LOWER(?) AND LOWER(city)=LOWER(?) LIMIT 1");
mysqli_stmt_bind_param($dup, "iss", $user_id, $party_name, $city);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);
if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode(["status" => "error", "message" => "Party already exists in this city"]);
    exit;
}

$cols = get_table_columns($con, 'party');
$deals_ids = json_decode($_POST['deals_ids_json'] ?? '[]', true);
if (!is_array($deals_ids)) $deals_ids = [];
$deals_ids = array_values(array_unique(array_filter(array_map('intval', $deals_ids))));
$deals_csv = implode(",", $deals_ids);
$field_map = [
    'party_name' => ['v' => $party_name, 't' => 'str'],
    'city' => ['v' => $city, 't' => 'str'],
    'state' => ['v' => post_str('state'), 't' => 'str'],
    'pin_code' => ['v' => post_str('pin_code'), 't' => 'str'],
    'area' => ['v' => post_str('area'), 't' => 'str'],
    'contact_no' => ['v' => post_str('contact_no'), 't' => 'str'],
    'gst_no' => ['v' => post_str('gst_no'), 't' => 'str'],
    'pan_no' => ['v' => post_str('pan_no'), 't' => 'str'],
    'email' => ['v' => post_str('email'), 't' => 'str'],
    'opening_balance' => ['v' => post_num('opening_balance', 0), 't' => 'num'],
    'balance_type' => ['v' => post_str('balance_type', 'DB'), 't' => 'str'],
    'user_id' => ['v' => $user_id, 't' => 'int'],
    'party_role_byr' => ['v' => post_bool('party_role_byr'), 't' => 'int'],
    'party_role_slr' => ['v' => post_bool('party_role_slr'), 't' => 'int'],
    'party_role_sb' => ['v' => post_bool('party_role_sb'), 't' => 'int'],
    'party_role_bb' => ['v' => post_bool('party_role_bb'), 't' => 'int'],
    'address1' => ['v' => post_str('address1'), 't' => 'str'],
    'address2' => ['v' => post_str('address2'), 't' => 'str'],
    'address3' => ['v' => post_str('address3'), 't' => 'str'],
    'address4' => ['v' => post_str('address4'), 't' => 'str'],
    'group_name' => ['v' => post_str('group_name'), 't' => 'str'],
    'category' => ['v' => post_str('category'), 't' => 'str'],
    'zone_area' => ['v' => post_str('zone_area'), 't' => 'str'],
    'sms_ac' => ['v' => post_str('sms_ac'), 't' => 'str'],
    'mobile_no' => ['v' => post_str('mobile_no'), 't' => 'str'],
    'sms_ow' => ['v' => post_str('sms_ow'), 't' => 'str'],
    'trans' => ['v' => post_str('trans'), 't' => 'str'],
    'proprietor' => ['v' => post_str('proprietor'), 't' => 'str'],
    'fssai_no' => ['v' => post_str('fssai_no'), 't' => 'str'],
    'lock_date' => ['v' => post_str('lock_date'), 't' => 'str'],
    'party_type' => ['v' => post_str('party_type'), 't' => 'str'],
    'is_active' => ['v' => post_bool('is_active'), 't' => 'int'],
    'multiple_sms_session' => ['v' => post_bool('multiple_sms_session'), 't' => 'int'],
    'cr_day' => ['v' => post_int('cr_day', 0), 't' => 'int'],
    'comp_group' => ['v' => post_str('comp_group'), 't' => 'str'],
    'co_name' => ['v' => post_str('co_name'), 't' => 'str'],
    'remarks' => ['v' => post_str('remarks'), 't' => 'str'],
    'office_address1' => ['v' => post_str('office_address1'), 't' => 'str'],
    'office_address2' => ['v' => post_str('office_address2'), 't' => 'str'],
    'office_address3' => ['v' => post_str('office_address3'), 't' => 'str'],
    'office_city' => ['v' => post_str('office_city'), 't' => 'str'],
    'office_state' => ['v' => post_str('office_state'), 't' => 'str'],
    'office_pin' => ['v' => post_str('office_pin'), 't' => 'str'],
    'office_phone' => ['v' => post_str('office_phone'), 't' => 'str'],
    'office_mobile' => ['v' => post_str('office_mobile'), 't' => 'str'],
    'wp1' => ['v' => post_str('wp1'), 't' => 'str'],
    'wp2' => ['v' => post_str('wp2'), 't' => 'str'],
    'wp3' => ['v' => post_str('wp3'), 't' => 'str'],
    'wp4' => ['v' => post_str('wp4'), 't' => 'str'],
    'sms_reg' => ['v' => post_bool('sms_reg'), 't' => 'int'],
    'wp_reg' => ['v' => post_bool('wp_reg'), 't' => 'int'],
    'email_reg' => ['v' => post_bool('email_reg'), 't' => 'int'],
    'default_product_id' => ['v' => post_int('default_product_id', 0), 't' => 'int'],
    'default_brand_id' => ['v' => post_int('default_brand_id', 0), 't' => 'int'],
    'deals_ids' => ['v' => $deals_csv, 't' => 'str']
];

$insert_cols = [];
$insert_vals = [];
foreach ($field_map as $col => $meta) {
    if (!isset($cols[$col])) continue;
    $insert_cols[] = $col;
    $insert_vals[] = sql_val($con, $meta['v'], $meta['t']);
}

mysqli_begin_transaction($con);
try {
    $sql = "INSERT INTO party (" . implode(",", $insert_cols) . ") VALUES (" . implode(",", $insert_vals) . ")";
    if (!mysqli_query($con, $sql)) throw new Exception('Failed to save party');
    $party_id = (int)mysqli_insert_id($con);
    save_related($con, $party_id, $user_id);
    mysqli_commit($con);
    echo json_encode(["status" => "success", "message" => "Party added"]);
} catch (Throwable $e) {
    mysqli_rollback($con);
    echo json_encode(["status" => "error", "message" => "Failed: " . $e->getMessage()]);
}
?>

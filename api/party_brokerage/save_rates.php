<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$party_id = (int)($_POST['party_id'] ?? 0);
$rows_json = $_POST['rows_json'] ?? '[]';

function has_column($con, $table, $column) {
    $table_safe = mysqli_real_escape_string($con, $table);
    $column_safe = mysqli_real_escape_string($con, $column);
    $q = mysqli_query($con, "SHOW COLUMNS FROM `{$table_safe}` LIKE '{$column_safe}'");
    return $q && mysqli_num_rows($q) > 0;
}

if ($party_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid party'
    ]);
    exit;
}

$check_party = mysqli_prepare($con, 'SELECT party_id FROM party WHERE party_id=? AND user_id=? LIMIT 1');
mysqli_stmt_bind_param($check_party, 'ii', $party_id, $user_id);
mysqli_stmt_execute($check_party);
$party_res = mysqli_stmt_get_result($check_party);
if (!$party_res || !mysqli_fetch_assoc($party_res)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Party not found'
    ]);
    exit;
}

$rows = json_decode($rows_json, true);
if (!is_array($rows)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid rows'
    ]);
    exit;
}

mysqli_begin_transaction($con);

try {
    $valid_product_stmt = mysqli_prepare($con, 'SELECT product_id FROM product WHERE product_id=? AND user_id=? LIMIT 1');
    if (!$valid_product_stmt) {
        throw new Exception('Product validation prepare failed');
    }

    $del = mysqli_prepare($con, 'DELETE FROM party_brokerage_rate WHERE user_id=? AND party_id=?');
    if (!$del) {
        throw new Exception('Delete prepare failed');
    }
    mysqli_stmt_bind_param($del, 'ii', $user_id, $party_id);
    if (!mysqli_stmt_execute($del)) {
        throw new Exception('Delete failed');
    }

    $has_created_at = has_column($con, 'party_brokerage_rate', 'created_at');
    $has_updated_at = has_column($con, 'party_brokerage_rate', 'updated_at');

    $insert_sql = 'INSERT INTO party_brokerage_rate (party_id, product_id, slr_type, slr_rt, byr_type, byr_rt, user_id';
    $insert_vals = ') VALUES (?, ?, ?, ?, ?, ?, ?';
    if ($has_created_at) {
        $insert_sql .= ', created_at';
        $insert_vals .= ', NOW()';
    }
    if ($has_updated_at) {
        $insert_sql .= ', updated_at';
        $insert_vals .= ', NOW()';
    }
    $insert_sql .= $insert_vals . ')';

    $ins = mysqli_prepare($con, $insert_sql);
    if (!$ins) {
        throw new Exception('Insert prepare failed');
    }

    $seen_products = [];

    foreach ($rows as $r) {
        $product_id = (int)($r['product_id'] ?? 0);
        if ($product_id <= 0) {
            continue;
        }
        if (isset($seen_products[$product_id])) {
            continue;
        }
        $seen_products[$product_id] = true;

        mysqli_stmt_bind_param($valid_product_stmt, 'ii', $product_id, $user_id);
        mysqli_stmt_execute($valid_product_stmt);
        $valid_res = mysqli_stmt_get_result($valid_product_stmt);
        if (!$valid_res || !mysqli_fetch_assoc($valid_res)) {
            throw new Exception('Invalid product selected');
        }

        $slr_type = strtoupper(trim((string)($r['slr_type'] ?? 'PERCENT')));
        $byr_type = strtoupper(trim((string)($r['byr_type'] ?? 'PERCENT')));
        $allowed_types = ['PERCENT', 'MANUAL', 'PACK', 'QUINTAL'];
        if (!in_array($slr_type, $allowed_types, true)) {
            $slr_type = 'PERCENT';
        }
        if (!in_array($byr_type, $allowed_types, true)) {
            $byr_type = 'PERCENT';
        }

        $slr_rt_raw = trim((string)($r['slr_rt'] ?? ''));
        $byr_rt_raw = trim((string)($r['byr_rt'] ?? ''));
        $slr_rt = ($slr_rt_raw === '') ? null : (float)$slr_rt_raw;
        $byr_rt = ($byr_rt_raw === '') ? null : (float)$byr_rt_raw;

        $slr_rt_db = ($slr_rt === null) ? 0.0 : $slr_rt;
        $byr_rt_db = ($byr_rt === null) ? 0.0 : $byr_rt;
        mysqli_stmt_bind_param($ins, 'iisdsdi', $party_id, $product_id, $slr_type, $slr_rt_db, $byr_type, $byr_rt_db, $user_id);
        if (!mysqli_stmt_execute($ins)) {
            throw new Exception('Insert failed');
        }
    }

    if (count($seen_products) === 0) {
        throw new Exception('At least one valid product is required');
    }

    mysqli_commit($con);
    echo json_encode([
        'status' => 'success',
        'message' => 'Brokerage setup saved'
    ]);
} catch (Throwable $e) {
    mysqli_rollback($con);
    echo json_encode([
        'status' => 'error',
        'message' => 'Save failed: ' . $e->getMessage()
    ]);
}
?>

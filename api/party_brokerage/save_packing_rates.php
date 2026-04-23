<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$party_id = (int)($_POST['party_id'] ?? 0);
$rows_json = $_POST['rows_json'] ?? '[]';

function has_table($con, $table) {
    $table_safe = mysqli_real_escape_string($con, $table);
    $q = mysqli_query($con, "SHOW TABLES LIKE '{$table_safe}'");
    return $q && mysqli_num_rows($q) > 0;
}

if ($party_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid party'
    ]);
    exit;
}

if (!has_table($con, 'party_brokerage_packing_rate')) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Packing table missing'
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
    $del = mysqli_prepare($con, 'DELETE FROM party_brokerage_packing_rate WHERE user_id=? AND party_id=?');
    if (!$del) {
        throw new Exception('Delete prepare failed');
    }
    mysqli_stmt_bind_param($del, 'ii', $user_id, $party_id);
    if (!mysqli_stmt_execute($del)) {
        throw new Exception('Delete failed');
    }

    $ins = mysqli_prepare(
        $con,
        'INSERT INTO party_brokerage_packing_rate (party_id, packing, slr_rt, byr_rt, user_id, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, NOW(), NOW())'
    );
    if (!$ins) {
        throw new Exception('Insert prepare failed');
    }

    $has_any = false;
    foreach ($rows as $r) {
        $packing = trim((string)($r['packing'] ?? ''));
        $slr_rt_raw = trim((string)($r['slr_rt'] ?? ''));
        $byr_rt_raw = trim((string)($r['byr_rt'] ?? ''));
        if ($packing === '' && $slr_rt_raw === '' && $byr_rt_raw === '') {
            continue;
        }
        if ($packing === '') {
            throw new Exception('Packing name required');
        }
        $slr_rt = ($slr_rt_raw === '') ? 0.0 : (float)$slr_rt_raw;
        $byr_rt = ($byr_rt_raw === '') ? 0.0 : (float)$byr_rt_raw;
        mysqli_stmt_bind_param($ins, 'isddi', $party_id, $packing, $slr_rt, $byr_rt, $user_id);
        if (!mysqli_stmt_execute($ins)) {
            throw new Exception('Insert failed');
        }
        $has_any = true;
    }

    if (!$has_any) {
        throw new Exception('At least one packing row is required');
    }

    mysqli_commit($con);
    echo json_encode([
        'status' => 'success',
        'message' => 'Packing setup saved'
    ]);
} catch (Throwable $e) {
    mysqli_rollback($con);
    echo json_encode([
        'status' => 'error',
        'message' => 'Save failed: ' . $e->getMessage()
    ]);
}
?>

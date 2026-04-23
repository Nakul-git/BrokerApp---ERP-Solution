<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$bank_id = (int)($_POST['bank_id'] ?? 0);
$bank_name = trim($_POST['bank_name'] ?? '');
$branch = trim($_POST['branch'] ?? '');
$ifsc_code = strtoupper(trim($_POST['ifsc_code'] ?? ''));
$pin = trim($_POST['pin'] ?? '');

if ($bank_id <= 0 || $bank_name === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid data'
    ]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    'SELECT bank_id FROM bank WHERE user_id=? AND LOWER(bank_name)=LOWER(?) AND bank_id<>? LIMIT 1'
);
mysqli_stmt_bind_param($dup, 'isi', $user_id, $bank_name, $bank_id);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Bank already exists'
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    'UPDATE bank SET bank_name=?, branch=?, ifsc_code=?, pin=? WHERE bank_id=? AND user_id=?'
);

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Update failed'
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'ssssii', $bank_name, $branch, $ifsc_code, $pin, $bank_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Updated'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Update failed'
    ]);
}
?>
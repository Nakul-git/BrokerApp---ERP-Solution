<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$description = trim($_POST['description'] ?? '');
$narration_type = trim($_POST['narration_type'] ?? '');
$receipt_payment = trim($_POST['receipt_payment'] ?? '');

$valid_types = [
    'Cash Bank Line - 1',
    'Cash Bank Line - 2',
    'Cash Receipt Line - 1',
    'Cash Receipt Line - 2',
    'Journal Line - 1',
    'Journal Line - 2',
    'All'
];
$valid_receipts = ['Receipt', 'Payment', 'Both'];

if ($description === '' || $narration_type === '' || $receipt_payment === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Description, type and receipt/payment are required'
    ]);
    exit;
}

if (!in_array($narration_type, $valid_types, true)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid narration type'
    ]);
    exit;
}

if (!in_array($receipt_payment, $valid_receipts, true)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid receipt/payment value'
    ]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    'SELECT narration_id FROM narration WHERE user_id=? AND LOWER(description)=LOWER(?) LIMIT 1'
);
mysqli_stmt_bind_param($dup, 'is', $user_id, $description);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Narration already exists'
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    'INSERT INTO narration (description, narration_type, receipt_payment, user_id) VALUES (?, ?, ?, ?)'
);

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed'
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'sssi', $description, $narration_type, $receipt_payment, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Narration added'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed'
    ]);
}
?>
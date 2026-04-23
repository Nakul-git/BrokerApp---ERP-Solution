<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$narration_id = (int)($_POST['narration_id'] ?? 0);
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

if ($narration_id <= 0 || $description === '' || $narration_type === '' || $receipt_payment === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid data'
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
    'SELECT narration_id FROM narration WHERE user_id=? AND LOWER(description)=LOWER(?) AND narration_id<>? LIMIT 1'
);
mysqli_stmt_bind_param($dup, 'isi', $user_id, $description, $narration_id);
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
    'UPDATE narration SET description=?, narration_type=?, receipt_payment=? WHERE narration_id=? AND user_id=?'
);

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Update failed'
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'sssii', $description, $narration_type, $receipt_payment, $narration_id, $user_id);

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
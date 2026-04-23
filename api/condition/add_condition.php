<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$term_description = trim($_POST['term_description'] ?? '');
$is_default = isset($_POST['is_default']) ? 1 : 0;
$packing_condition = isset($_POST['packing_condition']) ? 1 : 0;
$loading_condition = isset($_POST['loading_condition']) ? 1 : 0;
$payment_condition = isset($_POST['payment_condition']) ? 1 : 0;
$application_items_json = trim($_POST['application_items_json'] ?? '[]');

if ($term_description === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Term description required'
    ]);
    exit;
}

if (json_decode($application_items_json, true) === null && $application_items_json !== 'null') {
    $application_items_json = '[]';
}

$dup = mysqli_prepare(
    $con,
    'SELECT condition_id FROM condition_master WHERE user_id=? AND LOWER(term_description)=LOWER(?) LIMIT 1'
);
mysqli_stmt_bind_param($dup, 'is', $user_id, $term_description);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Condition already exists'
    ]);
    exit;
}

if ($is_default === 1) {
    $reset = mysqli_prepare($con, 'UPDATE condition_master SET is_default=0 WHERE user_id=?');
    mysqli_stmt_bind_param($reset, 'i', $user_id);
    mysqli_stmt_execute($reset);
}

$stmt = mysqli_prepare(
    $con,
    'INSERT INTO condition_master (term_description, is_default, packing_condition, loading_condition, payment_condition, application_items_json, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)'
);

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed'
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'siiiisi', $term_description, $is_default, $packing_condition, $loading_condition, $payment_condition, $application_items_json, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Condition added'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed'
    ]);
}
?>
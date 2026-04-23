<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$sort_order = (int)($_POST['sort_order'] ?? 0);
$main_group_name = trim($_POST['main_group_name'] ?? '');
$group_name = trim($_POST['group_name'] ?? '');
$group_type = trim($_POST['group_type'] ?? 'Liabilities');
$maintain_bill_outstanding = strtoupper(trim($_POST['maintain_bill_outstanding'] ?? 'N'));
$suppress_trial_balance = strtoupper(trim($_POST['suppress_trial_balance'] ?? 'N'));
$address_details_req = strtoupper(trim($_POST['address_details_req'] ?? 'N'));
$general_ledger = strtoupper(trim($_POST['general_ledger'] ?? 'N'));

$valid_group_types = ['Income', 'Expenditure', 'Liabilities', 'Assets'];
if (!in_array($group_type, $valid_group_types, true)) {
    $group_type = 'Liabilities';
}

$yn = ['Y', 'N'];
if (!in_array($maintain_bill_outstanding, $yn, true)) $maintain_bill_outstanding = 'N';
if (!in_array($suppress_trial_balance, $yn, true)) $suppress_trial_balance = 'N';
if (!in_array($address_details_req, $yn, true)) $address_details_req = 'N';
if (!in_array($general_ledger, $yn, true)) $general_ledger = 'N';

if ($group_name === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Group name required'
    ]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    'SELECT group_id FROM group_master WHERE user_id=? AND LOWER(group_name)=LOWER(?) LIMIT 1'
);
mysqli_stmt_bind_param($dup, 'is', $user_id, $group_name);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Group already exists'
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    'INSERT INTO group_master (sort_order, main_group_name, group_name, group_type, maintain_bill_outstanding, suppress_trial_balance, address_details_req, general_ledger, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
);

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed'
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'isssssssi', $sort_order, $main_group_name, $group_name, $group_type, $maintain_bill_outstanding, $suppress_trial_balance, $address_details_req, $general_ledger, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Group added'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed'
    ]);
}
?>
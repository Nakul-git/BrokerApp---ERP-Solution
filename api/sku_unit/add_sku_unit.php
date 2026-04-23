<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$sku_name = trim($_POST['sku_name'] ?? '');
$sku_symbol = trim($_POST['sku_symbol'] ?? '');
$no_of_decimals = (int)($_POST['no_of_decimals'] ?? 2);
$conversion_unit = trim($_POST['conversion_unit'] ?? '');
$unit_symbol = trim($_POST['unit_symbol'] ?? '');
$conv_type = trim($_POST['conv_type'] ?? '*');
$conv_value = (float)($_POST['conv_value'] ?? 1);

if ($sku_name === '' || $sku_symbol === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'SKU name and symbol required'
    ]);
    exit;
}

if ($no_of_decimals < 0) {
    $no_of_decimals = 0;
}
if ($no_of_decimals > 8) {
    $no_of_decimals = 8;
}
if ($conv_type !== '*' && $conv_type !== '/') {
    $conv_type = '*';
}
if ($conv_value < 0) {
    $conv_value = 0;
}

$dup = mysqli_prepare(
    $con,
    'SELECT sku_id FROM sku_unit WHERE user_id=? AND LOWER(sku_name)=LOWER(?) LIMIT 1'
);
mysqli_stmt_bind_param($dup, 'is', $user_id, $sku_name);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'SKU already exists'
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    'INSERT INTO sku_unit (sku_name, sku_symbol, no_of_decimals, conversion_unit, unit_symbol, conv_type, conv_value, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
);

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed'
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'ssisssdi', $sku_name, $sku_symbol, $no_of_decimals, $conversion_unit, $unit_symbol, $conv_type, $conv_value, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'SKU added'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed'
    ]);
}
?>
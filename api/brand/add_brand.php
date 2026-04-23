<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$brand_name = trim($_POST['brand_name'] ?? '');
$sort_order = (int)($_POST['sort_order'] ?? 0);

if ($brand_name === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Brand description required'
    ]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    'SELECT brand_id FROM brand WHERE user_id=? AND LOWER(brand_name)=LOWER(?) LIMIT 1'
);
mysqli_stmt_bind_param($dup, 'is', $user_id, $brand_name);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Brand already exists'
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    'INSERT INTO brand (brand_name, sort_order, user_id) VALUES (?, ?, ?)'
);

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed'
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'sii', $brand_name, $sort_order, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Brand added'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed'
    ]);
}
?>

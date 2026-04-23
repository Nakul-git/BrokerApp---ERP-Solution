<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$brand_id = (int)($_POST['brand_id'] ?? 0);
$brand_name = trim($_POST['brand_name'] ?? '');
$sort_order = (int)($_POST['sort_order'] ?? 0);

if ($brand_id <= 0 || $brand_name === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid data'
    ]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    'SELECT brand_id FROM brand WHERE user_id=? AND LOWER(brand_name)=LOWER(?) AND brand_id<>? LIMIT 1'
);
mysqli_stmt_bind_param($dup, 'isi', $user_id, $brand_name, $brand_id);
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
    'UPDATE brand SET brand_name=?, sort_order=? WHERE brand_id=? AND user_id=?'
);

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Update failed'
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'siii', $brand_name, $sort_order, $brand_id, $user_id);

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

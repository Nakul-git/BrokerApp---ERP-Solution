<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

function table_exists($con, $table) {
    $safe = mysqli_real_escape_string($con, $table);
    $q = mysqli_query($con, "SHOW TABLES LIKE '{$safe}'");
    return $q && mysqli_num_rows($q) > 0;
}

$product_id = (int)($_POST['product_id'] ?? 0);
$user_id = get_master_scope_user_id();

if ($product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
    exit;
}

mysqli_begin_transaction($con);

try {
    if (table_exists($con, 'product_brand')) {
        $del_map = mysqli_prepare($con, 'DELETE FROM product_brand WHERE product_id=? AND user_id=?');
        mysqli_stmt_bind_param($del_map, 'ii', $product_id, $user_id);
        if (!mysqli_stmt_execute($del_map)) throw new Exception('Map delete failed');
    }

    if (table_exists($con, 'product_packing_detail')) {
        $del_pack = mysqli_prepare($con, 'DELETE FROM product_packing_detail WHERE product_id=? AND user_id=?');
        mysqli_stmt_bind_param($del_pack, 'ii', $product_id, $user_id);
        if (!mysqli_stmt_execute($del_pack)) throw new Exception('Packing delete failed');
    }

    $del = mysqli_prepare($con, 'DELETE FROM product WHERE product_id=? AND user_id=?');
    mysqli_stmt_bind_param($del, 'ii', $product_id, $user_id);
    if (!mysqli_stmt_execute($del)) throw new Exception('Delete failed');

    mysqli_commit($con);
    echo json_encode(['status' => 'success', 'message' => 'Deleted']);
} catch (Throwable $e) {
    mysqli_rollback($con);
    echo json_encode(['status' => 'error', 'message' => 'Delete failed: ' . $e->getMessage()]);
}
?>

<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$bank_id = (int)($_POST['bank_id'] ?? 0);

if ($bank_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid bank'
    ]);
    exit;
}

$stmt = mysqli_prepare($con, 'DELETE FROM bank WHERE bank_id=? AND user_id=?');
mysqli_stmt_bind_param($stmt, 'ii', $bank_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Deleted'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Delete failed'
    ]);
}
?>
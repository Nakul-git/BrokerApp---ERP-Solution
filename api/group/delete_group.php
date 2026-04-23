<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$group_id = (int)($_POST['group_id'] ?? 0);

if ($group_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid group'
    ]);
    exit;
}

$stmt = mysqli_prepare($con, 'DELETE FROM group_master WHERE group_id=? AND user_id=?');
mysqli_stmt_bind_param($stmt, 'ii', $group_id, $user_id);

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
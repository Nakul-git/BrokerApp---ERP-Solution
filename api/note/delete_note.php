<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$note_id = (int)($_POST['note_id'] ?? 0);

if ($note_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid note'
    ]);
    exit;
}

$stmt = mysqli_prepare($con, 'DELETE FROM note_master WHERE note_id=? AND user_id=?');
mysqli_stmt_bind_param($stmt, 'ii', $note_id, $user_id);

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
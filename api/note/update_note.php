<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$note_id = (int)($_POST['note_id'] ?? 0);
$note_description = trim($_POST['note_description'] ?? '');
$sort_order = (int)($_POST['sort_order'] ?? 0);
$applicable_sauda = isset($_POST['applicable_sauda']) ? 1 : 0;
$applicable_unloading = isset($_POST['applicable_unloading']) ? 1 : 0;

if ($note_id <= 0 || $note_description === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid data'
    ]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    'SELECT note_id FROM note_master WHERE user_id=? AND LOWER(note_description)=LOWER(?) AND note_id<>? LIMIT 1'
);
mysqli_stmt_bind_param($dup, 'isi', $user_id, $note_description, $note_id);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Note already exists'
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    'UPDATE note_master SET note_description=?, sort_order=?, applicable_sauda=?, applicable_unloading=? WHERE note_id=? AND user_id=?'
);

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Update failed'
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'siiiii', $note_description, $sort_order, $applicable_sauda, $applicable_unloading, $note_id, $user_id);

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
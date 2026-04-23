<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$setup_id = (int)($_POST['setup_id'] ?? 0);

if ($setup_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid row"]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    "DELETE FROM form_wise_book_setup WHERE setup_id=? AND user_id=?"
);

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Delete prepare failed"]);
    exit;
}

mysqli_stmt_bind_param($stmt, "ii", $setup_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["status" => "success", "message" => "Row deleted"]);
} else {
    echo json_encode(["status" => "error", "message" => "Delete failed"]);
}
?>

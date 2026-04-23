<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$line_id = (int)($_POST['line_id'] ?? 0);
$user_id = get_master_scope_user_id();

if ($line_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid line"
    ]);
    exit;
}

$stmt = mysqli_prepare($con, "DELETE FROM line_master WHERE line_id=? AND user_id=?");
mysqli_stmt_bind_param($stmt, "ii", $line_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "Deleted"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Delete failed"
    ]);
}
?>

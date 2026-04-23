<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$term_type_id = (int)($_POST['term_type_id'] ?? 0);
$description = trim($_POST['description'] ?? '');
$is_default = isset($_POST['is_default']) ? 1 : 0;

if ($term_type_id <= 0 || $description === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid data"
    ]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    "SELECT term_type_id FROM term_type WHERE user_id=? AND LOWER(description)=LOWER(?) AND term_type_id<>? LIMIT 1"
);
mysqli_stmt_bind_param($dup, "isi", $user_id, $description, $term_type_id);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        "status" => "error",
        "message" => "Term type already exists"
    ]);
    exit;
}

if ($is_default === 1) {
    $reset = mysqli_prepare($con, "UPDATE term_type SET is_default=0 WHERE user_id=?");
    mysqli_stmt_bind_param($reset, "i", $user_id);
    mysqli_stmt_execute($reset);
}

$stmt = mysqli_prepare(
    $con,
    "UPDATE term_type
     SET description=?, is_default=?
     WHERE term_type_id=? AND user_id=?"
);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Update failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "siii", $description, $is_default, $term_type_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "Updated"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Update failed"
    ]);
}
?>

<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$description = trim($_POST['description'] ?? '');
$is_default = isset($_POST['is_default']) ? 1 : 0;

if ($description === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Description required"
    ]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    "SELECT term_type_id FROM term_type WHERE user_id=? AND LOWER(description)=LOWER(?) LIMIT 1"
);
mysqli_stmt_bind_param($dup, "is", $user_id, $description);
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
    "INSERT INTO term_type (description, is_default, user_id) VALUES (?, ?, ?)"
);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "sii", $description, $is_default, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "Term type added"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
}
?>

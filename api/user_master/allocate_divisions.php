<?php
require "../session.php";
require "../user_master/ensure_allotment_columns.php";

header("Content-Type: application/json");
ensure_user_allotment_columns($con_company);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$admin_id = intval($_SESSION["user_id"] ?? 0);

// Check if user is admin
$adminStmt = mysqli_prepare($con_company, "SELECT is_admin FROM users WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($adminStmt, "i", $admin_id);
mysqli_stmt_execute($adminStmt);
$adminRes = mysqli_stmt_get_result($adminStmt);
$admin = mysqli_fetch_assoc($adminRes) ?: [];
mysqli_stmt_close($adminStmt);

if (!$admin || intval($admin["is_admin"]) !== 1) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$user_id = intval($_POST["user_id"] ?? 0);
$division_codes = (string)($_POST["division_codes"] ?? ""); // comma-separated like "DV,DV2,DV3"

if ($user_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid user ID"]);
    exit;
}

// Update user's allowed_divisions
$updateStmt = mysqli_prepare($con_company, "UPDATE users SET allowed_divisions = ? WHERE id = ?");
mysqli_stmt_bind_param($updateStmt, "si", $division_codes, $user_id);
$success = mysqli_stmt_execute($updateStmt);
mysqli_stmt_close($updateStmt);

if ($success) {
    echo json_encode([
        "status" => "success",
        "message" => "Division allocation updated",
        "user_id" => $user_id,
        "allocated_divisions" => $division_codes
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update allocation"]);
}

<?php
require "../session.php";
require "../user_master/ensure_allotment_columns.php";

header("Content-Type: application/json");

$user_id = intval($_SESSION["user_id"] ?? 0);

if ($user_id <= 0) {
    echo json_encode(["error" => "Not logged in", "user_id" => $user_id]);
    exit;
}

// Get user info
$userStmt = mysqli_prepare($con_company, "SELECT is_admin, allowed_divisions FROM users WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($userStmt, "i", $user_id);
mysqli_stmt_execute($userStmt);
$userRes = mysqli_stmt_get_result($userStmt);
$user = mysqli_fetch_assoc($userRes) ?: [];
mysqli_stmt_close($userStmt);

$is_admin = intval($user["is_admin"] ?? 0) === 1;

// Count total divisions
$countRes = mysqli_query($con_company, "SELECT COUNT(*) as cnt FROM division_master WHERE is_active = 1");
$countRow = mysqli_fetch_assoc($countRes);
$total_divisions = $countRow["cnt"] ?? 0;

// Try to get divisions
$divRes = mysqli_query($con_company, "SELECT division_id, div_name, div_code, is_active, company_id FROM division_master WHERE is_active = 1 LIMIT 10");
$divisions = [];
if ($divRes) {
    while ($row = mysqli_fetch_assoc($divRes)) {
        $divisions[] = $row;
    }
}

echo json_encode([
    "success" => true,
    "user_id" => $user_id,
    "is_admin" => $is_admin,
    "allowed_divisions" => $user["allowed_divisions"] ?? null,
    "total_active_divisions" => $total_divisions,
    "divisions_found" => count($divisions),
    "divisions" => $divisions,
    "query" => "SELECT division_id, div_name, div_code FROM division_master WHERE is_active = 1"
]);

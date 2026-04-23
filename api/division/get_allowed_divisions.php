<?php
require "../session.php";
require "../user_master/ensure_allotment_columns.php";

header("Content-Type: application/json");

$user_id = intval($_SESSION["user_id"] ?? 0);
$company_id = intval($_SESSION["selected_company_id"] ?? 0);

if ($user_id <= 0) {
    die(json_encode(["status" => "error", "message" => "Not logged in"]));
}

// Get user info
$userStmt = mysqli_prepare($con_company, "SELECT is_admin, allowed_divisions FROM users WHERE id = ? LIMIT 1");
if (!$userStmt) {
    die(json_encode(["status" => "error", "message" => "Database error: " . mysqli_error($con_company)]));
}

mysqli_stmt_bind_param($userStmt, "i", $user_id);
mysqli_stmt_execute($userStmt);
$userRes = mysqli_stmt_get_result($userStmt);
$user = mysqli_fetch_assoc($userRes);
mysqli_stmt_close($userStmt);

if (!$user) {
    die(json_encode(["status" => "error", "message" => "User not found"]));
}

$is_admin = intval($user["is_admin"] ?? 0) === 1;
$allowed_codes = array_values(array_filter(array_map("trim", explode(",", (string)($user["allowed_divisions"] ?? "")))));

$divisions = [];

// Admin users see all divisions
if ($is_admin) {
    $query = "SELECT division_id, div_name, div_code, is_default FROM division_master WHERE is_active = 1 ORDER BY div_name";
    $res = mysqli_query($con_company, $query);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $divisions[] = $row;
        }
    }
} 
// Regular users see only allocated divisions
else if (!empty($allowed_codes)) {
    // Build the IN clause manually to avoid bind_param issues
    $placeholders = array_fill(0, count($allowed_codes), "'%s'");
    $escaped_codes = array_map(function($code) use ($con_company) {
        return "'" . mysqli_real_escape_string($con_company, $code) . "'";
    }, $allowed_codes);
    
    $in_clause = implode(",", $escaped_codes);
    $query = "SELECT division_id, div_name, div_code, is_default FROM division_master WHERE is_active = 1 AND div_code IN ($in_clause) ORDER BY div_name";
    
    $res = mysqli_query($con_company, $query);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $divisions[] = $row;
        }
    }
}

echo json_encode([
    "status" => "success",
    "is_admin" => $is_admin ? 1 : 0,
    "divisions" => $divisions
]);
exit;

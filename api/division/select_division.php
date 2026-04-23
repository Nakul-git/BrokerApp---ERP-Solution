<?php
require "../session.php";
require "../user_master/ensure_allotment_columns.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die(json_encode(["status" => "error", "message" => "Invalid request"]));
}

$user_id = intval($_SESSION["user_id"] ?? 0);
$division_id = intval($_POST["division_id"] ?? 0);

if ($user_id <= 0 || $division_id <= 0) {
    die(json_encode(["status" => "error", "message" => "Invalid selection"]));
}

// Get user info
$userStmt = mysqli_prepare($con_company, "SELECT is_admin, allowed_divisions FROM users WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($userStmt, "i", $user_id);
mysqli_stmt_execute($userStmt);
$userRes = mysqli_stmt_get_result($userStmt);
$user = mysqli_fetch_assoc($userRes);
mysqli_stmt_close($userStmt);

if (!$user) {
    die(json_encode(["status" => "error", "message" => "User not found"]));
}

// Get division info
$divStmt = mysqli_prepare($con_company, "SELECT division_id, div_name, div_code FROM division_master WHERE division_id = ? LIMIT 1");
mysqli_stmt_bind_param($divStmt, "i", $division_id);
mysqli_stmt_execute($divStmt);
$divRes = mysqli_stmt_get_result($divStmt);
$division = mysqli_fetch_assoc($divRes);
mysqli_stmt_close($divStmt);

if (!$division) {
    die(json_encode(["status" => "error", "message" => "Division not found"]));
}

// Verify access
$is_admin = intval($user["is_admin"] ?? 0) === 1;
$allowed_codes = array_values(array_filter(array_map("trim", explode(",", (string)($user["allowed_divisions"] ?? "")))));

if (!$is_admin && !in_array($division["div_code"], $allowed_codes, true)) {
    die(json_encode(["status" => "error", "message" => "Division not allowed"]));
}

// Store in session
$_SESSION["selected_division_id"] = intval($division["division_id"]);
$_SESSION["selected_division_code"] = (string)($division["div_code"] ?? "");
$_SESSION["selected_division_name"] = (string)($division["div_name"] ?? "");

echo json_encode([
    "status" => "success",
    "division_id" => $_SESSION["selected_division_id"],
    "division_code" => $_SESSION["selected_division_code"],
    "division_name" => $_SESSION["selected_division_name"]
]);
exit;

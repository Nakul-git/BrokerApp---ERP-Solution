<?php
require "../session.php";
require "../user_master/ensure_allotment_columns.php";

header("Content-Type: application/json");
ensure_user_allotment_columns($con_company);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$user_id = intval($_SESSION["user_id"] ?? 0);
$company_id = intval($_POST["company_id"] ?? 0);

if ($user_id <= 0 || $company_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid selection"]);
    exit;
}

$userStmt = mysqli_prepare(
    $con_company,
    "SELECT is_admin, allowed_companies FROM users WHERE id = ? LIMIT 1"
);
mysqli_stmt_bind_param($userStmt, "i", $user_id);
mysqli_stmt_execute($userStmt);
$userRes = mysqli_stmt_get_result($userStmt);
$user = mysqli_fetch_assoc($userRes) ?: [];
mysqli_stmt_close($userStmt);

$companyStmt = mysqli_prepare(
    $con_company,
    "SELECT company_id, company_name, code, ask_division FROM company_master WHERE company_id = ? LIMIT 1"
);
mysqli_stmt_bind_param($companyStmt, "i", $company_id);
mysqli_stmt_execute($companyStmt);
$companyRes = mysqli_stmt_get_result($companyStmt);
$company = mysqli_fetch_assoc($companyRes) ?: null;
mysqli_stmt_close($companyStmt);

if (!$company) {
    echo json_encode(["status" => "error", "message" => "Company not found"]);
    exit;
}

$is_admin = intval($user["is_admin"] ?? 0) === 1;
$allowed_codes = array_values(array_filter(array_map("trim", explode(",", (string)($user["allowed_companies"] ?? "")))));

if (!$is_admin && !in_array((string)($company["code"] ?? ""), $allowed_codes, true)) {
    echo json_encode(["status" => "error", "message" => "Company not allowed"]);
    exit;
}

$_SESSION["selected_company_id"] = intval($company["company_id"]);
$_SESSION["selected_company_code"] = (string)($company["code"] ?? "");
$_SESSION["selected_company_name"] = (string)($company["company_name"] ?? "");

echo json_encode([
    "status" => "success",
    "company_id" => $_SESSION["selected_company_id"],
    "company_code" => $_SESSION["selected_company_code"],
    "company_name" => $_SESSION["selected_company_name"],
    "ask_division" => intval($company["ask_division"] ?? 0)
]);

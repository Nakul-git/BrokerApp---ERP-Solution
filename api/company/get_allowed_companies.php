<?php
require "../session.php";
require "../user_master/ensure_allotment_columns.php";

header("Content-Type: application/json");
ensure_user_allotment_columns($con_company);

$user_id = intval($_SESSION["user_id"] ?? 0);
if ($user_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
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

$is_admin = intval($user["is_admin"] ?? 0) === 1;
$allowed_codes = array_values(array_filter(array_map("trim", explode(",", (string)($user["allowed_companies"] ?? "")))));

if ($is_admin) {
    $sql = "SELECT company_id, company_name, code, start_date, end_date, file_name, is_default, ask_division FROM company_master ORDER BY company_name";
    $res = mysqli_query($con_company, $sql);
} else if (!empty($allowed_codes)) {
    $placeholders = implode(",", array_fill(0, count($allowed_codes), "?"));
    $sql = "SELECT company_id, company_name, code, start_date, end_date, file_name, is_default, ask_division
            FROM company_master
            WHERE code IN ($placeholders)
            ORDER BY company_name";
    $stmt = mysqli_prepare($con_company, $sql);
    $types = str_repeat("s", count($allowed_codes));
    mysqli_stmt_bind_param($stmt, $types, ...$allowed_codes);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
} else {
    echo json_encode([
        "status" => "success",
        "is_admin" => 0,
        "companies" => []
    ]);
    exit;
}

$companies = [];
while ($row = mysqli_fetch_assoc($res)) {
    $companies[] = $row;
}

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}

echo json_encode([
    "status" => "success",
    "is_admin" => $is_admin ? 1 : 0,
    "companies" => $companies
]);

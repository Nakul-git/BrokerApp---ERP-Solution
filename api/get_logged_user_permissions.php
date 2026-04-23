<?php
require "session.php";
require "user_master/ensure_allotment_columns.php";

header("Content-Type: application/json");
ensure_user_allotment_columns($con_company);

$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id) {
    echo json_encode([
        "status" => "error",
        "message" => "Not logged in"
    ]);
    exit;
}

$is_admin = 0;
$admin_sql = "SELECT is_admin FROM users WHERE id = ? LIMIT 1";
$admin_stmt = mysqli_prepare($con_company, $admin_sql);
if ($admin_stmt) {
    mysqli_stmt_bind_param($admin_stmt, "i", $user_id);
    mysqli_stmt_execute($admin_stmt);
    $admin_result = mysqli_stmt_get_result($admin_stmt);
    $admin_row = mysqli_fetch_assoc($admin_result);
    $is_admin = intval($admin_row["is_admin"] ?? 0);
    mysqli_stmt_close($admin_stmt);
}

$sql = "SELECT module_name
        FROM user_permissions
        WHERE user_id = ?
          AND can_view = 1";

$stmt = mysqli_prepare($con_company, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$sections = [];
$pages = [];
$section_whitelist = [
    "MASTERS",
    "TRANSACTIONS",
    "SCREENING",
    "PRINTING",
    "BILL SECTION",
    "ACCOUNTING",
    "USER SECURITY",
    "UTILITIES",
    "ACCOUNT GROUP",
    "MISC. MASTERS",
    "MASTER DATA"
];

while ($row = mysqli_fetch_assoc($result)) {
    $module = strtoupper(trim($row['module_name']));

    if (strpos($module, "PAGE:") === 0) {
        $pages[] = substr($module, 5);
        continue;
    }

    if (in_array($module, $section_whitelist, true)) {
        $sections[] = $module;
    }
}

echo json_encode([
    "status" => "success",
    "is_admin" => $is_admin,
    "selected_company_id" => $_SESSION['selected_company_id'] ?? null,
    "selected_company_code" => $_SESSION['selected_company_code'] ?? null,
    "selected_company_name" => $_SESSION['selected_company_name'] ?? null,
    "selected_division_id" => $_SESSION['selected_division_id'] ?? null,
    "selected_division_code" => $_SESSION['selected_division_code'] ?? null,
    "selected_division_name" => $_SESSION['selected_division_name'] ?? null,
    "allowed_sections" => array_values(array_unique($sections)),
    "allowed_pages" => array_values(array_unique($pages))
]);

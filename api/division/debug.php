<?php
require "../session.php";
require "../user_master/ensure_allotment_columns.php";

header("Content-Type: application/json");
ensure_user_allotment_columns($con_company);

$user_id = intval($_SESSION["user_id"] ?? 0);

// Debug info
$response = [
    "status" => "success",
    "user_id" => $user_id,
    "session_data" => [
        "selected_company_id" => $_SESSION["selected_company_id"] ?? null,
        "selected_company_code" => $_SESSION["selected_company_code"] ?? null,
        "selected_company_name" => $_SESSION["selected_company_name"] ?? null,
        "selected_division_id" => $_SESSION["selected_division_id"] ?? null,
        "selected_division_code" => $_SESSION["selected_division_code"] ?? null,
        "selected_division_name" => $_SESSION["selected_division_name"] ?? null,
    ]
];

if ($user_id > 0) {
    $userStmt = mysqli_prepare(
        $con_company,
        "SELECT id, name, is_admin, allowed_divisions FROM users WHERE id = ? LIMIT 1"
    );
    mysqli_stmt_bind_param($userStmt, "i", $user_id);
    mysqli_stmt_execute($userStmt);
    $userRes = mysqli_stmt_get_result($userStmt);
    $user = mysqli_fetch_assoc($userRes) ?: [];
    mysqli_stmt_close($userStmt);
    
    $response["user_data"] = $user;
    
    // Check divisions count
    $divStmt = mysqli_prepare(
        $con_company,
        "SELECT COUNT(*) as count FROM division_master WHERE is_active = 1"
    );
    mysqli_stmt_execute($divStmt);
    $divRes = mysqli_stmt_get_result($divStmt);
    $divCount = mysqli_fetch_assoc($divRes);
    mysqli_stmt_close($divStmt);
    
    $response["total_active_divisions"] = $divCount["count"] ?? 0;
}

echo json_encode($response);

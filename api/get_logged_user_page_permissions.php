<?php
require "session.php";

header("Content-Type: application/json");

$user_id = intval($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) {
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

$page = strtolower(trim($_GET["page"] ?? ""));
$section = strtoupper(trim($_GET["section"] ?? ""));

$permissions = [
    "v" => 0,
    "a" => 0,
    "e" => 0,
    "d" => 0,
    "p" => 0
];

if ($is_admin === 1) {
    echo json_encode([
        "status" => "success",
        "source" => "admin",
        "is_admin" => 1,
        "permissions" => [
            "v" => 1,
            "a" => 1,
            "e" => 1,
            "d" => 1,
            "p" => 1
        ]
    ]);
    exit;
}

if ($page !== "") {
    $normalized_page = strtoupper(basename($page));
    $normalized_page = preg_replace("/[^A-Z0-9]+/", "_", $normalized_page);
    $module_name = "PAGE:" . $normalized_page;
    $sql = "SELECT can_view, can_add, can_edit, can_delete, can_print
            FROM user_permissions
            WHERE user_id = ? AND module_name = ?
            LIMIT 1";
    $stmt = mysqli_prepare($con_company, $sql);
    mysqli_stmt_bind_param($stmt, "is", $user_id, $module_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($row) {
        $permissions = [
            "v" => intval($row["can_view"]),
            "a" => intval($row["can_add"]),
            "e" => intval($row["can_edit"]),
            "d" => intval($row["can_delete"]),
            "p" => intval($row["can_print"])
        ];

        echo json_encode([
            "status" => "success",
            "source" => "page",
            "is_admin" => 0,
            "permissions" => $permissions
        ]);
        exit;
    }

    // Strict mode for page access: no page row means no access.
    echo json_encode([
        "status" => "success",
        "source" => "page_missing",
        "is_admin" => 0,
        "permissions" => $permissions
    ]);
    exit;
}

if ($section !== "") {
    $sql = "SELECT can_view
            FROM user_permissions
            WHERE user_id = ? AND module_name = ?
            LIMIT 1";
    $stmt = mysqli_prepare($con_company, $sql);
    mysqli_stmt_bind_param($stmt, "is", $user_id, $section);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($row) {
        $permissions["v"] = intval($row["can_view"]);
    }
}

echo json_encode([
    "status" => "success",
    "source" => "section",
    "is_admin" => 0,
    "permissions" => $permissions
]);

<?php
require "../session.php";
require "bootstrap.php";

header("Content-Type: application/json");

$response = ["status" => "success"];

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Invalid request method");
    }

    ensure_role_master_tables($con_company);

    $role_id = intval($_POST["role_id"] ?? 0);
    if ($role_id <= 0) {
        throw new Exception("Invalid role id");
    }

    $roleStmt = mysqli_prepare($con_company, "SELECT role_name FROM roles WHERE id=? LIMIT 1");
    mysqli_stmt_bind_param($roleStmt, "i", $role_id);
    mysqli_stmt_execute($roleStmt);
    $roleRes = mysqli_stmt_get_result($roleStmt);
    $roleRow = mysqli_fetch_assoc($roleRes);
    mysqli_stmt_close($roleStmt);

    if (!$roleRow) {
        throw new Exception("Role not found");
    }

    $role_name = $roleRow["role_name"];

    $useStmt = mysqli_prepare($con_company, "SELECT COUNT(*) AS cnt FROM users WHERE role_name=?");
    mysqli_stmt_bind_param($useStmt, "s", $role_name);
    mysqli_stmt_execute($useStmt);
    $useRes = mysqli_stmt_get_result($useStmt);
    $useRow = mysqli_fetch_assoc($useRes);
    mysqli_stmt_close($useStmt);

    if (intval($useRow["cnt"] ?? 0) > 0) {
        throw new Exception("Role is assigned to users. Reassign users first.");
    }

    $delPerm = mysqli_prepare($con_company, "DELETE FROM role_permissions WHERE role_name=?");
    mysqli_stmt_bind_param($delPerm, "s", $role_name);
    mysqli_stmt_execute($delPerm);
    mysqli_stmt_close($delPerm);

    $delRole = mysqli_prepare($con_company, "DELETE FROM roles WHERE id=?");
    mysqli_stmt_bind_param($delRole, "i", $role_id);
    mysqli_stmt_execute($delRole);
    mysqli_stmt_close($delRole);
} catch (Exception $e) {
    $response["status"] = "error";
    $response["message"] = $e->getMessage();
}

echo json_encode($response);

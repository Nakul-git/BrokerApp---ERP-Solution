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

    $mode = strtolower(trim($_POST["mode"] ?? "add"));
    $role_id = intval($_POST["role_id"] ?? 0);
    $role_name = strtoupper(trim($_POST["role_name"] ?? ""));
    $description = trim($_POST["description"] ?? "");
    $is_active = isset($_POST["is_active"]) ? intval($_POST["is_active"]) : 1;
    $created_by = intval($_SESSION["user_id"] ?? 0);

    if ($role_name === "") {
        throw new Exception("Role name is required");
    }

    if ($mode === "add") {
        $stmt = mysqli_prepare($con_company, "INSERT INTO roles (role_name, description, is_active, created_by) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssii", $role_name, $description, $is_active, $created_by);
        if (!mysqli_stmt_execute($stmt)) {
            if (mysqli_errno($con_company) === 1062) {
                throw new Exception("Role name already exists");
            }
            throw new Exception(mysqli_error($con_company));
        }
        $role_id = intval(mysqli_insert_id($con_company));
        mysqli_stmt_close($stmt);
    } else {
        if ($role_id <= 0) {
            throw new Exception("Invalid role id");
        }

        $oldRoleName = "";
        $oldStmt = mysqli_prepare($con_company, "SELECT role_name FROM roles WHERE id=? LIMIT 1");
        mysqli_stmt_bind_param($oldStmt, "i", $role_id);
        mysqli_stmt_execute($oldStmt);
        $oldRes = mysqli_stmt_get_result($oldStmt);
        $oldRow = mysqli_fetch_assoc($oldRes);
        mysqli_stmt_close($oldStmt);

        if (!$oldRow) {
            throw new Exception("Role not found");
        }
        $oldRoleName = $oldRow["role_name"];

        $stmt = mysqli_prepare($con_company, "UPDATE roles SET role_name=?, description=?, is_active=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssii", $role_name, $description, $is_active, $role_id);
        if (!mysqli_stmt_execute($stmt)) {
            if (mysqli_errno($con_company) === 1062) {
                throw new Exception("Role name already exists");
            }
            throw new Exception(mysqli_error($con_company));
        }
        mysqli_stmt_close($stmt);

        if (strcasecmp($oldRoleName, $role_name) !== 0) {
            $uStmt = mysqli_prepare($con_company, "UPDATE users SET role_name=? WHERE role_name=?");
            mysqli_stmt_bind_param($uStmt, "ss", $role_name, $oldRoleName);
            mysqli_stmt_execute($uStmt);
            mysqli_stmt_close($uStmt);
        }

        $dStmt = mysqli_prepare($con_company, "DELETE FROM role_permissions WHERE role_name=?");
        mysqli_stmt_bind_param($dStmt, "s", $oldRoleName);
        mysqli_stmt_execute($dStmt);
        mysqli_stmt_close($dStmt);
    }

    $rights = $_POST["rights"] ?? [];
    $page_rights = $_POST["page_rights"] ?? [];

    $insertSql = "INSERT INTO role_permissions (role_name, module_name, can_view, can_add, can_edit, can_delete, can_print) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $ins = mysqli_prepare($con_company, $insertSql);

    foreach ($rights as $module => $perm) {
        $can_view = isset($perm["v"]) ? 1 : 0;
        if ($can_view !== 1) continue;

        $can_add = 0;
        $can_edit = 0;
        $can_delete = 0;
        $can_print = 0;

        mysqli_stmt_bind_param($ins, "ssiiiii", $role_name, $module, $can_view, $can_add, $can_edit, $can_delete, $can_print);
        mysqli_stmt_execute($ins);
    }

    foreach ($page_rights as $page => $perm) {
        $can_view = isset($perm["v"]) ? 1 : 0;
        $can_add = isset($perm["a"]) ? 1 : 0;
        $can_edit = isset($perm["e"]) ? 1 : 0;
        $can_delete = isset($perm["d"]) ? 1 : 0;
        $can_print = isset($perm["p"]) ? 1 : 0;

        if (($can_view + $can_add + $can_edit + $can_delete + $can_print) === 0) continue;

        $normalized_page = strtoupper(trim($page));
        $normalized_page = preg_replace("/[^A-Z0-9]+/", "_", $normalized_page);
        $module_name = "PAGE:" . $normalized_page;

        mysqli_stmt_bind_param($ins, "ssiiiii", $role_name, $module_name, $can_view, $can_add, $can_edit, $can_delete, $can_print);
        mysqli_stmt_execute($ins);
    }

    mysqli_stmt_close($ins);

    $response["role_id"] = $role_id;
    $response["role_name"] = $role_name;
} catch (Exception $e) {
    $response["status"] = "error";
    $response["message"] = $e->getMessage();
}

echo json_encode($response);

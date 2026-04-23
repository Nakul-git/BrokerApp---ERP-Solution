<?php
require "../session.php";
require "bootstrap.php";

header("Content-Type: application/json");

$response = [
    "status" => "success",
    "permissions" => []
];

try {
    ensure_role_master_tables($con_company);

    $role_name = trim($_GET["role_name"] ?? "");
    if ($role_name === "") {
        throw new Exception("Role name missing");
    }

    $sql = "SELECT module_name, can_view, can_add, can_edit, can_delete, can_print FROM role_permissions WHERE role_name = ?";
    $stmt = mysqli_prepare($con_company, $sql);
    mysqli_stmt_bind_param($stmt, "s", $role_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $response["permissions"][$row["module_name"]] = [
            "v" => intval($row["can_view"]),
            "a" => intval($row["can_add"]),
            "e" => intval($row["can_edit"]),
            "d" => intval($row["can_delete"]),
            "p" => intval($row["can_print"])
        ];
    }

    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    $response["status"] = "error";
    $response["message"] = $e->getMessage();
}

echo json_encode($response);

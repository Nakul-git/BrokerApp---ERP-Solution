<?php
require "../session.php";

header("Content-Type: application/json");

$response = [
    "status" => "success",
    "permissions" => []
];

try {

    if (!isset($_GET['user_id'])) {
        throw new Exception("User ID missing");
    }

    $user_id = intval($_GET['user_id']);

    $sql = "SELECT 
                module_name,
                can_view,
                can_add,
                can_edit,
                can_delete,
                can_print
            FROM user_permissions
            WHERE user_id = ?";

    $stmt = mysqli_prepare($con_company, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $permissions = [];

    while ($row = mysqli_fetch_assoc($result)) {

        $permissions[$row['module_name']] = [
            "v" => intval($row['can_view']),
            "a" => intval($row['can_add']),
            "e" => intval($row['can_edit']),
            "d" => intval($row['can_delete']),
            "p" => intval($row['can_print'])
        ];
    }

    $response["permissions"] = $permissions;

    mysqli_stmt_close($stmt);

} catch (Exception $e) {

    $response["status"] = "error";
    $response["message"] = $e->getMessage();
}

echo json_encode($response);
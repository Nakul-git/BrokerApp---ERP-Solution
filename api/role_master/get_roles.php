<?php
require "../session.php";
require "bootstrap.php";

header("Content-Type: application/json");

$response = [
    "status" => "success",
    "roles" => []
];

try {
    ensure_role_master_tables($con_company);

    $sql = "SELECT id, role_name, description, is_active FROM roles ORDER BY role_name ASC";
    $result = mysqli_query($con_company, $sql);

    if (!$result) {
        throw new Exception(mysqli_error($con_company));
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $response["roles"][] = [
            "id" => intval($row["id"]),
            "role_name" => $row["role_name"],
            "description" => $row["description"] ?? "",
            "is_active" => intval($row["is_active"])
        ];
    }
} catch (Exception $e) {
    $response["status"] = "error";
    $response["message"] = $e->getMessage();
}

echo json_encode($response);

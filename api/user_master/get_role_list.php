<?php
require "../session.php";
require "../role_master/bootstrap.php";

header("Content-Type: application/json");

$response = [
    "status" => "success",
    "roles" => []
];

try {
    ensure_role_master_tables($con_company);

    $sql = "SELECT role_name FROM roles WHERE is_active=1 ORDER BY role_name ASC";
    $res = mysqli_query($con_company, $sql);

    if (!$res) {
        throw new Exception(mysqli_error($con_company));
    }

    while ($row = mysqli_fetch_assoc($res)) {
        $response["roles"][] = strtoupper($row["role_name"]);
    }

    $response["roles"] = array_values(array_unique($response["roles"]));
} catch (Exception $e) {
    $response["status"] = "error";
    $response["message"] = $e->getMessage();
}

echo json_encode($response);

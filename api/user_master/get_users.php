<?php
require "../session.php";
require "ensure_allotment_columns.php";

header("Content-Type: application/json");
ensure_user_allotment_columns($con_company);

/* ==========================
   DEFAULT RESPONSE
========================== */

$current_user_id = intval($_SESSION['user_id'] ?? 0);

$response = [
    "status" => "success",
    "users" => [],
    "current_user_id" => $current_user_id,
    "logged_is_admin" => 0
];

try {

    /* ==========================
       GET LOGGED USER ADMIN STATUS
    =========================== */

    if ($current_user_id > 0) {

        $adminQuery = mysqli_prepare(
            $con_company,
            "SELECT is_admin FROM users WHERE id=? LIMIT 1"
        );

        mysqli_stmt_bind_param($adminQuery, "i", $current_user_id);
        mysqli_stmt_execute($adminQuery);

        $adminResult = mysqli_stmt_get_result($adminQuery);
        $adminData = mysqli_fetch_assoc($adminResult);

        $response["logged_is_admin"] = intval($adminData["is_admin"] ?? 0);
    }

    /* ==========================
       GET ALL USERS
    =========================== */

    $sql = "SELECT 
                id,
                name,
                email,
                password,
                role_name,
                is_admin,
                is_active,
                created_by,
                created_at,
                allowed_divisions,
                allowed_companies
            FROM users
            ORDER BY name ASC";

    $result = mysqli_query($con_company, $sql);

    if (!$result) {
        throw new Exception(mysqli_error($con_company));
    }

    while ($row = mysqli_fetch_assoc($result)) {

        $response["users"][] = [
            "id"         => intval($row["id"]),
            "name"       => $row["name"],
            "email"      => $row["email"],
            "password"   => $row["password"],   // hashed
            "role_name"  => $row["role_name"],
            "is_admin"   => intval($row["is_admin"]),
            "is_active"  => intval($row["is_active"]),
            "created_by" => $row["created_by"],
            "created_at" => $row["created_at"],
            "allowed_divisions" => $row["allowed_divisions"] ?? "",
            "allowed_companies" => $row["allowed_companies"] ?? ""
        ];
    }

} catch (Exception $e) {

    $response["status"] = "error";
    $response["message"] = $e->getMessage();
}

/* ==========================
   OUTPUT JSON
========================== */

echo json_encode($response);

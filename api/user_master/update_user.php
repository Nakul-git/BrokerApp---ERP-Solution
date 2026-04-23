<?php
require "../session.php";
require "ensure_allotment_columns.php";

header("Content-Type: application/json");
ensure_user_allotment_columns($con_company);

/* ==========================
   VALIDATE REQUEST
========================== */

if($_SERVER["REQUEST_METHOD"] !== "POST"){
    echo json_encode([
        "status"=>"error",
        "message"=>"Invalid request"
    ]);
    exit;
}

$user_id   = intval($_POST["user_id"] ?? 0);
$user_name = trim($_POST["user_name"] ?? "");
$password  = trim($_POST["password"] ?? "");
$role_name = trim($_POST["role_name"] ?? "");
$is_admin  = isset($_POST["is_admin"]) ? intval($_POST["is_admin"]) : 0;
$is_active = isset($_POST["is_active"]) ? intval($_POST["is_active"]) : 1;
$allowed_divisions = array_values(array_filter(array_map("trim", $_POST["division_codes"] ?? [])));
$allowed_companies = array_values(array_filter(array_map("trim", $_POST["company_codes"] ?? [])));
$allowed_divisions_csv = implode(",", array_unique($allowed_divisions));
$allowed_companies_csv = implode(",", array_unique($allowed_companies));

if($user_id <= 0){
    echo json_encode([
        "status"=>"error",
        "message"=>"Invalid user ID"
    ]);
    exit;
}

if($user_name === ""){
    echo json_encode([
        "status"=>"error",
        "message"=>"Username is required"
    ]);
    exit;
}

/* ==========================
   DUPLICATE CHECK
========================== */

$stmt = mysqli_prepare(
    $con_company,
    "SELECT id FROM users WHERE name = ? AND id != ? LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "si", $user_name, $user_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if($res && mysqli_num_rows($res) > 0){
    echo json_encode([
        "status"=>"error",
        "message"=>"Username already exists"
    ]);
    exit;
}

/* ==========================
   BUILD UPDATE QUERY
========================== */

if($password !== ""){

    // hash new password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = mysqli_prepare(
        $con_company,
        "UPDATE users 
         SET name=?, password=?, role_name=?, is_admin=?, is_active=?, allowed_divisions=?, allowed_companies=? 
         WHERE id=?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sssiissi",
        $user_name,
        $hashed_password,
        $role_name,
        $is_admin,
        $is_active,
        $allowed_divisions_csv,
        $allowed_companies_csv,
        $user_id
    );

}else{

    // do not change password
    $stmt = mysqli_prepare(
        $con_company,
        "UPDATE users 
         SET name=?, role_name=?, is_admin=?, is_active=?, allowed_divisions=?, allowed_companies=? 
         WHERE id=?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ssiissi",
        $user_name,
        $role_name,
        $is_admin,
        $is_active,
        $allowed_divisions_csv,
        $allowed_companies_csv,
        $user_id
    );
}

$exec = mysqli_stmt_execute($stmt);

if(!$exec){
    echo json_encode([
        "status"=>"error",
        "message"=>"Update failed"
    ]);
    exit;
}

echo json_encode([
    "status"=>"success",
    "message"=>"User updated successfully",
    "user_id"=>$user_id
]);

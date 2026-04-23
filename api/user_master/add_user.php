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
        "status" => "error",
        "message" => "Invalid request method"
    ]);
    exit;
}

$user_name  = trim($_POST["user_name"] ?? "");
$password   = trim($_POST["password"] ?? "");
$role_name  = trim($_POST["role_name"] ?? "");
$is_admin   = isset($_POST["is_admin"]) ? intval($_POST["is_admin"]) : 0;
$is_active  = isset($_POST["is_active"]) ? intval($_POST["is_active"]) : 1;
$allowed_divisions = array_values(array_filter(array_map("trim", $_POST["division_codes"] ?? [])));
$allowed_companies = array_values(array_filter(array_map("trim", $_POST["company_codes"] ?? [])));

$created_by = $_SESSION["user_id"] ?? 0;

/* ==========================
   BASIC VALIDATION
========================== */

if($user_name === ""){
    echo json_encode([
        "status" => "error",
        "message" => "Username is required"
    ]);
    exit;
}

if($password === ""){
    echo json_encode([
        "status" => "error",
        "message" => "Password is required"
    ]);
    exit;
}

/* ==========================
   DUPLICATE CHECK
========================== */

$user_name = mysqli_real_escape_string($con_company, $user_name);

$check_sql = "SELECT id FROM users WHERE name = '$user_name' LIMIT 1";
$check_res = mysqli_query($con_company, $check_sql);

if($check_res && mysqli_num_rows($check_res) > 0){
    echo json_encode([
        "status" => "error",
        "message" => "Username already exists"
    ]);
    exit;
}

/* ==========================
   HASH PASSWORD
========================== */

$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$allowed_divisions_csv = mysqli_real_escape_string($con_company, implode(",", array_unique($allowed_divisions)));
$allowed_companies_csv = mysqli_real_escape_string($con_company, implode(",", array_unique($allowed_companies)));

/* ==========================
   INSERT USER
========================== */

$role_name = mysqli_real_escape_string($con_company, $role_name);

$sql = "
    INSERT INTO users 
        (name, password, role_name, is_admin, is_active, created_by, allowed_divisions, allowed_companies)
    VALUES
        ('$user_name', '$hashed_password', '$role_name', $is_admin, $is_active, $created_by, '$allowed_divisions_csv', '$allowed_companies_csv')
";

$res = mysqli_query($con_company, $sql);

if(!$res){
    echo json_encode([
        "status" => "error",
        "message" => "Database insert failed"
    ]);
    exit;
}

$new_id = mysqli_insert_id($con_company);

echo json_encode([
    "status" => "success",
    "message" => "User created successfully",
    "user_id" => $new_id
]);

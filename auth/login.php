<?php
require "../api/session.php";

header("Content-Type: application/json");

if($_SERVER["REQUEST_METHOD"] !== "POST"){
    echo json_encode([
        "status"=>"error",
        "message"=>"Invalid request"
    ]);
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if($username === "" || $password === ""){
    echo json_encode([
        "status"=>"error",
        "message"=>"Username and password required"
    ]);
    exit;
}

/* ==========================
   FIND USER BY USERNAME
========================== */

$stmt = mysqli_prepare(
    $con_company,
    "SELECT id, password FROM users WHERE name = ? LIMIT 1"
);

mysqli_stmt_bind_param($stmt,"s",$username);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

/* ==========================
   VERIFY PASSWORD
========================== */

if($user && password_verify($password, $user['password'])){

    $_SESSION['user_id'] = $user['id'];
    unset($_SESSION['selected_company_id'], $_SESSION['selected_company_code'], $_SESSION['selected_company_name']);

    echo json_encode([
        "status"=>"success",
        "message"=>"Login success"
    ]);

}else{

    echo json_encode([
        "status"=>"error",
        "message"=>"Invalid username or password"
    ]);
}

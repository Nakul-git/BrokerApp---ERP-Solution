<?php
require "../session.php";

header("Content-Type: application/json");

/* ==========================
   VALIDATE REQUEST
========================== */

if($_SERVER["REQUEST_METHOD"] !== "POST"){
    echo json_encode([
        "status"  => "error",
        "message" => "Invalid request"
    ]);
    exit;
}

$user_id = intval($_POST["user_id"] ?? 0);

if($user_id <= 0){
    echo json_encode([
        "status"  => "error",
        "message" => "Invalid user ID"
    ]);
    exit;
}

/* ==========================
   PREVENT SELF DELETE
========================== */

$current_user_id = intval($_SESSION["user_id"] ?? 0);

if($user_id === $current_user_id){
    echo json_encode([
        "status"  => "error",
        "message" => "You cannot delete yourself"
    ]);
    exit;
}

/* ==========================
   CHECK USER EXISTS
========================== */

$check = mysqli_prepare(
    $con_company,
    "SELECT is_admin FROM users WHERE id=? LIMIT 1"
);

mysqli_stmt_bind_param($check, "i", $user_id);
mysqli_stmt_execute($check);
$res = mysqli_stmt_get_result($check);
$user = mysqli_fetch_assoc($res);

if(!$user){
    echo json_encode([
        "status"  => "error",
        "message" => "User not found"
    ]);
    exit;
}

/* ==========================
   PREVENT DELETING LAST ADMIN
========================== */

if(intval($user["is_admin"]) === 1){

    $adminCountQuery = mysqli_query(
        $con_company,
        "SELECT COUNT(*) as total FROM users WHERE is_admin=1"
    );

    $adminData = mysqli_fetch_assoc($adminCountQuery);

    if(intval($adminData["total"]) <= 1){
        echo json_encode([
            "status"  => "error",
            "message" => "Cannot delete the last administrator"
        ]);
        exit;
    }
}

/* ==========================
   DELETE USER
========================== */

$stmt = mysqli_prepare(
    $con_company,
    "DELETE FROM users WHERE id=?"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);

$exec = mysqli_stmt_execute($stmt);

if(!$exec){
    echo json_encode([
        "status"  => "error",
        "message" => "Delete failed"
    ]);
    exit;
}

/* ==========================
   SUCCESS
========================== */

echo json_encode([
    "status"  => "success",
    "message" => "User deleted successfully"
]);
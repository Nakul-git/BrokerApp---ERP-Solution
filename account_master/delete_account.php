<?php
header("Content-Type: application/json");
require "../session.php";
require_once "../master_scope.php";

$user_id = get_master_scope_user_id();
$account_id = isset($_POST["account_id"]) ? (int)$_POST["account_id"] : 0;

if ($account_id <= 0) {
    echo json_encode([ "status" => "error", "message" => "Invalid account id" ]);
    exit;
}

mysqli_query($con, "DELETE FROM account_division_balance WHERE account_id=" . (int)$account_id . " AND user_id=" . (int)$user_id);
mysqli_query($con, "DELETE FROM account_bank_details WHERE account_id=" . (int)$account_id . " AND user_id=" . (int)$user_id);

$stmt = mysqli_prepare(
    $con,
    "DELETE FROM account_master WHERE account_id=? AND user_id=?"
);
mysqli_stmt_bind_param($stmt, "ii", $account_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([ "status" => "success", "message" => "Deleted" ]);
} else {
    echo json_encode([ "status" => "error", "message" => "Failed" ]);
}
?>

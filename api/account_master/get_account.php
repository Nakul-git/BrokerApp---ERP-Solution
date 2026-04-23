<?php
header("Content-Type: application/json");
require "../session.php";
require_once "../master_scope.php";

$user_id = get_master_scope_user_id();
$account_id = isset($_GET["account_id"]) ? (int)$_GET["account_id"] : 0;

if ($account_id <= 0) {
    echo json_encode([ "status" => "error", "message" => "Invalid account id" ]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    "SELECT account_id, group_name, account_name, account_name_hi,
            other_details_address, broker, trans, prop, city_name, state_name, pin_code, category,
            tin, cst, gst, pan, email_id, acc_type, credit_d, credit_limit, contact_person,
            office_phone, fax, mobile, sms,
            lock_date, other_info, is_active, is_default, hand_book_ac
     FROM account_master
     WHERE user_id=? AND account_id=?"
);
if (!$stmt) {
    echo json_encode([ "status" => "error", "message" => "Prepare failed", "error" => mysqli_error($con) ]);
    exit;
}
mysqli_stmt_bind_param($stmt, "ii", $user_id, $account_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$account = mysqli_fetch_assoc($res);

if (!$account) {
    echo json_encode([ "status" => "error", "message" => "Not found" ]);
    exit;
}

$divStmt = mysqli_prepare(
    $con,
    "SELECT d.division_id, d.div_name,
            COALESCE(b.opening_balance, 0) AS opening_balance,
            COALESCE(b.dc, 'D') AS dc
     FROM division_master d
     LEFT JOIN account_division_balance b
       ON b.division_id = d.division_id
      AND b.account_id = ?
      AND b.user_id = ?
     WHERE d.user_id = ?
       AND b.account_id IS NOT NULL
     ORDER BY d.div_name"
);
$divisions = [];
if ($divStmt) {
    mysqli_stmt_bind_param($divStmt, "iii", $account_id, $user_id, $user_id);
    mysqli_stmt_execute($divStmt);
    $divRes = mysqli_stmt_get_result($divStmt);
    while ($row = mysqli_fetch_assoc($divRes)) {
        $divisions[] = $row;
    }
}

$bankStmt = mysqli_prepare(
    $con,
    "SELECT bank_id, ac_holder, ac_number, bank_name
     FROM account_bank_details
     WHERE user_id=? AND account_id=?
     ORDER BY bank_id"
);
$banks = [];
if ($bankStmt) {
    mysqli_stmt_bind_param($bankStmt, "ii", $user_id, $account_id);
    mysqli_stmt_execute($bankStmt);
    $bankRes = mysqli_stmt_get_result($bankStmt);
    while ($row = mysqli_fetch_assoc($bankRes)) {
        $banks[] = $row;
    }
}

echo json_encode([
    "status" => "success",
    "account" => $account,
    "divisions" => $divisions,
    "bank_details" => $banks
]);
?>

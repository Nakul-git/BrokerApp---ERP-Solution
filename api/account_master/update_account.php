<?php
header("Content-Type: application/json");
require "../session.php";
require_once "../master_scope.php";

$user_id = get_master_scope_user_id();

$account_id = isset($_POST["account_id"]) ? (int)$_POST["account_id"] : 0;
$group_name = trim($_POST["group_name"] ?? "");
$account_name = trim($_POST["account_name"] ?? "");
$account_name_hi = trim($_POST["account_name_hi"] ?? "");
$other_details_address = trim($_POST["other_details_address"] ?? "");
$broker = trim($_POST["broker"] ?? "");
$trans = trim($_POST["trans"] ?? "");
$prop = trim($_POST["prop"] ?? "");
$city_name = trim($_POST["city_name"] ?? "");
$state_name = trim($_POST["state_name"] ?? "");
$pin_code = trim($_POST["pin_code"] ?? "");
$category = trim($_POST["category"] ?? "");
$tin = trim($_POST["tin"] ?? "");
$cst = trim($_POST["cst"] ?? "");
$gst = trim($_POST["gst"] ?? "");
$pan = trim($_POST["pan"] ?? "");
$email_id = trim($_POST["email_id"] ?? "");
$acc_type = trim($_POST["acc_type"] ?? "");
$credit_d = (float)($_POST["credit_d"] ?? 0);
$credit_limit = (float)($_POST["credit_limit"] ?? 0);
$contact_person = trim($_POST["contact_person"] ?? "");
$office_phone = trim($_POST["office_phone"] ?? "");
$fax = trim($_POST["fax"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$sms = trim($_POST["sms"] ?? "");
$lock_date = trim($_POST["lock_date"] ?? "");
$other_info = trim($_POST["other_info"] ?? "");
$is_active = isset($_POST["is_active"]) ? "Y" : "N";
$is_default = isset($_POST["is_default"]) ? "Y" : "N";
$hand_book_ac = isset($_POST["hand_book_ac"]) ? "Y" : "N";

if ($account_id <= 0 || $group_name === "" || $account_name === "") {
    echo json_encode([ "status" => "error", "message" => "Required fields missing" ]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    "SELECT account_id FROM account_master WHERE user_id=? AND LOWER(account_name)=LOWER(?) AND account_id<>? LIMIT 1"
);
if (!$dup) {
    echo json_encode([ "status" => "error", "message" => "Prepare failed", "error" => mysqli_error($con) ]);
    exit;
}
mysqli_stmt_bind_param($dup, "isi", $user_id, $account_name, $account_id);
mysqli_stmt_execute($dup);
$dupRes = mysqli_stmt_get_result($dup);
if ($dupRes && mysqli_fetch_assoc($dupRes)) {
    echo json_encode([ "status" => "error", "message" => "Account already exists" ]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    "UPDATE `account_master`
     SET `group_name`=?, `account_name`=?, `account_name_hi`=?,
         `other_details_address`=?, `broker`=?, `trans`=?, `prop`=?, `city_name`=?, `state_name`=?, `pin_code`=?, `category`=?,
         `tin`=?, `cst`=?, `gst`=?, `pan`=?, `email_id`=?, `acc_type`=?, `credit_d`=?, `credit_limit`=?, `contact_person`=?,
         `office_phone`=?, `fax`=?, `mobile`=?, `sms`=?,
         `lock_date`=NULLIF(?, ''), `other_info`=?, `is_active`=?, `is_default`=?, `hand_book_ac`=?
     WHERE `account_id`=? AND `user_id`=?"
);
if (!$stmt) {
    echo json_encode([ "status" => "error", "message" => "Prepare failed", "error" => mysqli_error($con) ]);
    exit;
}
mysqli_stmt_bind_param(
    $stmt,
    "sssssssssssssssssddssssssssssii",
    $group_name,
    $account_name,
    $account_name_hi,
    $other_details_address,
    $broker,
    $trans,
    $prop,
    $city_name,
    $state_name,
    $pin_code,
    $category,
    $tin,
    $cst,
    $gst,
    $pan,
    $email_id,
    $acc_type,
    $credit_d,
    $credit_limit,
    $contact_person,
    $office_phone,
    $fax,
    $mobile,
    $sms,
    $lock_date,
    $other_info,
    $is_active,
    $is_default,
    $hand_book_ac,
    $account_id,
    $user_id
);

if (!mysqli_stmt_execute($stmt)) {
    echo json_encode([ "status" => "error", "message" => "Failed", "error" => mysqli_error($con) ]);
    exit;
}

mysqli_query($con, "DELETE FROM account_division_balance WHERE account_id=" . (int)$account_id . " AND user_id=" . (int)$user_id);
mysqli_query($con, "DELETE FROM account_bank_details WHERE account_id=" . (int)$account_id . " AND user_id=" . (int)$user_id);

$divisions = json_decode($_POST["divisions"] ?? "[]", true);
if (is_array($divisions)) {
    foreach ($divisions as $row) {
        $division_id = (int)($row["division_id"] ?? 0);
        if ($division_id <= 0) continue;
        $opening = (float)($row["opening_balance"] ?? 0);
        $dc = strtoupper(trim($row["dc"] ?? "D"));
        if ($dc !== "C") $dc = "D";
        $ins = mysqli_prepare(
            $con,
            "INSERT INTO account_division_balance (account_id, division_id, opening_balance, dc, user_id)
             VALUES (?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($ins, "iidsi", $account_id, $division_id, $opening, $dc, $user_id);
        mysqli_stmt_execute($ins);
    }
}

$banks = json_decode($_POST["bank_details"] ?? "[]", true);
if (is_array($banks)) {
    foreach ($banks as $row) {
        $ac_holder = trim($row["ac_holder"] ?? "");
        $ac_number = trim($row["ac_number"] ?? "");
        $bank_name = trim($row["bank_name"] ?? "");
        if ($ac_holder === "" && $ac_number === "" && $bank_name === "") continue;
        $ins = mysqli_prepare(
            $con,
            "INSERT INTO account_bank_details (account_id, ac_holder, ac_number, bank_name, user_id)
             VALUES (?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($ins, "isssi", $account_id, $ac_holder, $ac_number, $bank_name, $user_id);
        mysqli_stmt_execute($ins);
    }
}

echo json_encode([ "status" => "success", "message" => "Account updated" ]);
?>

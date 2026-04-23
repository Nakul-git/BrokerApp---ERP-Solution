<?php
header("Content-Type: application/json");
require "../session.php";
require_once "../master_scope.php";

$user_id = get_master_scope_user_id();

function v($key) {
    return trim($_POST[$key] ?? '');
}

$div_name = v("div_name");
$div_code = v("div_code");
$company_name = v("company_name");
$is_active = isset($_POST["is_active"]) ? 1 : 0;
$is_default = isset($_POST["is_default"]) ? 1 : 0;

$address1 = v("address1");
$address2 = v("address2");
$address3 = v("address3");
$address4 = v("address4");

$place_name = v("place_name");
$state_name = v("state_name");
$pin_code = v("pin_code");

$proprietor = v("proprietor");
$pan_no = v("pan_no");

$phone_office = v("phone_office");
$mobile_no = v("mobile_no");
$phone_fax = v("phone_fax");

$email_id = v("email_id");
$website = v("website");

$tin_no = v("tin_no");
$tan_no = v("tan_no");
$gst_no = v("gst_no");

$bank_name = v("bank_name");
$bank1 = v("bank1");
$bank2 = v("bank2");
$bank3 = v("bank3");
$bank4 = v("bank4");

$jurisdiction = v("jurisdiction");

$top_line_header = v("top_line_header");
$middle_line = v("middle_line");
$bottom_footer = v("bottom_footer");

$fixed_terms = v("fixed_terms");

$sms_domain = v("sms_domain");
$sms_user = v("sms_user");
$sms_password = v("sms_password");
$sms_port = v("sms_port");

$smtp_client = v("smtp_client");
$email_user = v("email_user");
$email_pwd = v("email_pwd");
$email_port = v("email_port");


if ($div_name === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Division name required"
    ]);
    exit;
}


/* ✅ company DB */

$dup = mysqli_prepare(
    $con_company,
    "SELECT division_id
     FROM division_master
     WHERE user_id=? AND LOWER(div_name)=LOWER(?)
     LIMIT 1"
);

mysqli_stmt_bind_param($dup, "is", $user_id, $div_name);
mysqli_stmt_execute($dup);

$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {

    echo json_encode([
        "status" => "error",
        "message" => "Division already exists"
    ]);
    exit;
}


if ($is_default === 1) {

    $reset = mysqli_prepare(
        $con_company,
        "UPDATE division_master
         SET is_default=0
         WHERE user_id=?"
    );

    mysqli_stmt_bind_param($reset, "i", $user_id);
    mysqli_stmt_execute($reset);
}


$stmt = mysqli_prepare(
    $con_company,
    "INSERT INTO division_master (
        div_name, div_code, company_name,
        is_active, is_default,
        address1, address2, address3, address4,
        place_name, state_name, pin_code,
        proprietor, pan_no,
        phone_office, mobile_no, phone_fax,
        email_id, website,
        tin_no, tan_no, gst_no,
        bank_name, bank1, bank2, bank3, bank4,
        jurisdiction,
        top_line_header, middle_line, bottom_footer,
        fixed_terms,
        sms_domain, sms_user, sms_password, sms_port,
        smtp_client, email_user, email_pwd, email_port,
        user_id
     )
     VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
     )"
);


mysqli_stmt_bind_param(
    $stmt,
    "sssii" . str_repeat("s", 35) . "i",

    $div_name,
    $div_code,
    $company_name,

    $is_active,
    $is_default,

    $address1,
    $address2,
    $address3,
    $address4,

    $place_name,
    $state_name,
    $pin_code,

    $proprietor,
    $pan_no,

    $phone_office,
    $mobile_no,
    $phone_fax,

    $email_id,
    $website,

    $tin_no,
    $tan_no,
    $gst_no,

    $bank_name,
    $bank1,
    $bank2,
    $bank3,
    $bank4,

    $jurisdiction,

    $top_line_header,
    $middle_line,
    $bottom_footer,

    $fixed_terms,

    $sms_domain,
    $sms_user,
    $sms_password,
    $sms_port,

    $smtp_client,
    $email_user,
    $email_pwd,
    $email_port,

    $user_id
);


if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "status" => "success",
        "message" => "Division added"
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Failed"
    ]);
}
?>

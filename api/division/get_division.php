<?php
header("Content-Type: application/json");
require "../session.php";
require_once "../master_scope.php";

$user_id = get_master_scope_user_id();


/* ✅ use company DB */

$stmt = mysqli_prepare(
    $con_company,
    "SELECT
        division_id,
        div_name,
        div_code,
        company_name,
        is_active,
        is_default,
        address1,
        address2,
        address3,
        address4,
        place_name,
        state_name,
        pin_code,
        proprietor,
        pan_no,
        phone_office,
        mobile_no,
        phone_fax,
        email_id,
        website,
        tin_no,
        tan_no,
        gst_no,
        bank_name,
        bank1,
        bank2,
        bank3,
        bank4,
        jurisdiction,
        top_line_header,
        middle_line,
        bottom_footer,
        fixed_terms,
        sms_domain,
        sms_user,
        sms_password,
        sms_port,
        smtp_client,
        email_user,
        email_pwd,
        email_port
     FROM division_master
     WHERE user_id=?
     ORDER BY div_name"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$res = mysqli_stmt_get_result($stmt);

$data = [];

while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}

echo json_encode($data);
?>

<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$setup_id = (int)($_POST['setup_id'] ?? 0);
$module_id = (int)($_POST['module_id'] ?? 0);
$description = trim($_POST['description'] ?? '');
$parameter_type = trim($_POST['parameter_type'] ?? 'Add');
$order_no = trim($_POST['order_no'] ?? '');
$percent_value = trim($_POST['percent_value'] ?? '');
$calculation = trim($_POST['calculation'] ?? 'Percent');
$active = trim($_POST['active'] ?? 'Y');
$applicable_on = trim($_POST['applicable_on'] ?? '');
$posting_ac = trim($_POST['posting_ac'] ?? '');
$outer_column = trim($_POST['outer_column'] ?? '');
$cst_vat_other = trim($_POST['cst_vat_other'] ?? '');
$si_flag = trim($_POST['si_flag'] ?? '');
$from_value = trim($_POST['from_value'] ?? '');
$end_value = trim($_POST['end_value'] ?? '');
$rate_edt = trim($_POST['rate_edt'] ?? 'Y');
$amt_edt = trim($_POST['amt_edt'] ?? 'Y');
$amt_round = trim($_POST['amt_round'] ?? 'Y');
$division_name = trim($_POST['division_name'] ?? '');

if ($setup_id <= 0 || $module_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid setup"]);
    exit;
}

if ($description === '') {
    echo json_encode(["status" => "error", "message" => "Description required"]);
    exit;
}

$parameter_type = (strcasecmp($parameter_type, 'Less') === 0) ? 'Less' : 'Add';
$calculation = (strcasecmp($calculation, 'Amount') === 0) ? 'Amount' : 'Percent';
$active = (strcasecmp($active, 'N') === 0) ? 'N' : 'Y';
$rate_edt = (strcasecmp($rate_edt, 'N') === 0) ? 'N' : 'Y';
$amt_edt = (strcasecmp($amt_edt, 'N') === 0) ? 'N' : 'Y';
$amt_round = (strcasecmp($amt_round, 'N') === 0) ? 'N' : 'Y';

$check = mysqli_prepare(
    $con,
    "SELECT setup_id FROM add_less_parameter_setup WHERE setup_id=? AND user_id=? AND module_id=? LIMIT 1"
);
if (!$check) {
    echo json_encode(["status" => "error", "message" => "Setup check failed"]);
    exit;
}
mysqli_stmt_bind_param($check, "iii", $setup_id, $user_id, $module_id);
mysqli_stmt_execute($check);
$check_res = mysqli_stmt_get_result($check);
if (!$check_res || !mysqli_fetch_assoc($check_res)) {
    echo json_encode(["status" => "error", "message" => "Row not found"]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    "SELECT setup_id FROM add_less_parameter_setup
     WHERE user_id=? AND module_id=? AND LOWER(description)=LOWER(?) AND setup_id<>?
     LIMIT 1"
);
if ($dup) {
    mysqli_stmt_bind_param($dup, "iisi", $user_id, $module_id, $description, $setup_id);
    mysqli_stmt_execute($dup);
    $dup_res = mysqli_stmt_get_result($dup);
    if ($dup_res && mysqli_fetch_assoc($dup_res)) {
        echo json_encode(["status" => "error", "message" => "Row already exists"]);
        exit;
    }
}

$stmt = mysqli_prepare(
    $con,
    "UPDATE add_less_parameter_setup
     SET description=?, parameter_type=?, order_no=NULLIF(?, ''), percent_value=NULLIF(?, ''), calculation=?,
         active=?, applicable_on=?, posting_ac=?, outer_column=?, cst_vat_other=?, si_flag=?,
         from_value=NULLIF(?, ''), end_value=NULLIF(?, ''), rate_edt=?, amt_edt=?, amt_round=?, division_name=?
     WHERE setup_id=? AND user_id=? AND module_id=?"
);

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Update prepare failed"]);
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "sssssssssssssssssiii",
    $description,
    $parameter_type,
    $order_no,
    $percent_value,
    $calculation,
    $active,
    $applicable_on,
    $posting_ac,
    $outer_column,
    $cst_vat_other,
    $si_flag,
    $from_value,
    $end_value,
    $rate_edt,
    $amt_edt,
    $amt_round,
    $division_name,
    $setup_id,
    $user_id,
    $module_id
);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["status" => "success", "message" => "Row updated"]);
} else {
    echo json_encode(["status" => "error", "message" => "Update failed"]);
}
?>

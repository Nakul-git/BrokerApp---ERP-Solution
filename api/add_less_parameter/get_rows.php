<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$module_id = (int)($_POST['module_id'] ?? 0);

if ($module_id <= 0) {
    echo json_encode([]);
    exit;
}

$check = mysqli_prepare($con, "SELECT module_id FROM add_less_entry_module WHERE module_id=? AND user_id=? LIMIT 1");
if (!$check) {
    echo json_encode([]);
    exit;
}
mysqli_stmt_bind_param($check, "ii", $module_id, $user_id);
mysqli_stmt_execute($check);
$check_res = mysqli_stmt_get_result($check);
if (!$check_res || !mysqli_fetch_assoc($check_res)) {
    echo json_encode([]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    "SELECT setup_id, module_id, description, parameter_type, order_no, percent_value, calculation,
            active, applicable_on, posting_ac, outer_column, cst_vat_other, si_flag,
            from_value, end_value, rate_edt, amt_edt, amt_round, division_name
     FROM add_less_parameter_setup
     WHERE user_id=? AND module_id=?
     ORDER BY order_no, setup_id"
);

if (!$stmt) {
    echo json_encode([]);
    exit;
}

mysqli_stmt_bind_param($stmt, "ii", $user_id, $module_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$data = [];
while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}

echo json_encode($data);
?>

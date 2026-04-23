<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();
$transport_id = (int)($_POST['transport_id'] ?? 0);
$transport_name = trim($_POST['transport_name'] ?? '');
$line_name = trim($_POST['line_name'] ?? '');
$address = trim($_POST['address'] ?? '');
$address1 = trim($_POST['address1'] ?? '');
$city_id = (int)($_POST['city_id'] ?? 0);
if ($city_id <= 0) {
    $city_id = null;
}
$station = trim($_POST['station'] ?? '');
$state_name = trim($_POST['state_name'] ?? '');
$pin_code = trim($_POST['pin_code'] ?? '');
$contact_person = trim($_POST['contact_person'] ?? '');
$phone_office = trim($_POST['phone_office'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$email = trim($_POST['email'] ?? '');
$pan = trim($_POST['pan'] ?? '');
$other_info = trim($_POST['other_info'] ?? '');
$applicable_divisions = trim($_POST['applicable_divisions'] ?? '');

if ($transport_id <= 0 || $transport_name === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid data'
    ]);
    exit;
}

$dup = mysqli_prepare(
    $con,
    'SELECT transport_id FROM transport WHERE user_id=? AND LOWER(transport_name)=LOWER(?) AND transport_id<>? LIMIT 1'
);
mysqli_stmt_bind_param($dup, 'isi', $user_id, $transport_name, $transport_id);
mysqli_stmt_execute($dup);
$dup_res = mysqli_stmt_get_result($dup);

if ($dup_res && mysqli_fetch_assoc($dup_res)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Transport already exists'
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $con,
    'UPDATE transport SET transport_name=?, line_name=?, address=?, address1=?, city_id=?, station=?, state_name=?, pin_code=?, contact_person=?, phone_office=?, mobile=?, email=?, pan=?, other_info=?, applicable_divisions=? WHERE transport_id=? AND user_id=?'
);

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Update failed'
    ]);
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    'ssssissssssssssii',
    $transport_name,
    $line_name,
    $address,
    $address1,
    $city_id,
    $station,
    $state_name,
    $pin_code,
    $contact_person,
    $phone_office,
    $mobile,
    $email,
    $pan,
    $other_info,
    $applicable_divisions,
    $transport_id,
    $user_id
);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Updated'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Update failed'
    ]);
}
?>

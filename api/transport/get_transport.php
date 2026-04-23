<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();

$stmt = mysqli_prepare(
    $con,
    "SELECT
        t.transport_id,
        t.transport_name,
        t.line_name,
        t.address,
        t.address1,
        t.station,
        t.state_name,
        t.city_id,
        c.city_name,
        s.state_name AS city_state_name,
        t.pin_code,
        t.contact_person,
        t.phone_office,
        t.mobile,
        t.email,
        t.pan,
        t.other_info,
        t.applicable_divisions
     FROM transport t
     LEFT JOIN city c ON t.city_id = c.city_id
     LEFT JOIN states s ON c.state_id = s.id
     WHERE t.user_id=?
     ORDER BY t.transport_name"
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

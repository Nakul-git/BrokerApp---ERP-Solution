<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();

$stmt = mysqli_prepare(
    $con,
    "SELECT
        district.district_id,
        district.district_name,
        district.state_id,
        district.population,
        district.area_sq_kms,
        states.state_name
     FROM district
     LEFT JOIN states ON district.state_id = states.id
     WHERE district.user_id = ?
     ORDER BY district.district_name"
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

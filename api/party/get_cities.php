<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();

$q = mysqli_prepare($con, "
SELECT
    city.city_id,
    city.city_name,
    states.state_name
FROM city
LEFT JOIN states ON city.state_id = states.id
WHERE city.user_id = ?
ORDER BY city.city_name
");

mysqli_stmt_bind_param($q, "i", $user_id);
mysqli_stmt_execute($q);

$res = mysqli_stmt_get_result($q);
$data = [];

while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}

echo json_encode($data);
?>

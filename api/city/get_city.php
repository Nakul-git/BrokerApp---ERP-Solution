<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();

function has_city_column($con, $column_name) {
    $safe = mysqli_real_escape_string($con, $column_name);
    $q = mysqli_query($con, "SHOW COLUMNS FROM city LIKE '{$safe}'");
    return $q && mysqli_num_rows($q) > 0;
}

$select = [
    "city.city_id",
    "city.city_name",
    "city.state_id",
    "states.state_name",
    "city.district_id",
    "district.district_name"

];

$optional = ["pin_code", "std_code", "party_type", "distance_kms"];
foreach ($optional as $column) {
    if (has_city_column($con, $column)) {
        $select[] = "city." . $column;
    }
}

$sql = "SELECT " . implode(",", $select) . "
FROM city
LEFT JOIN states ON city.state_id = states.id
LEFT JOIN district ON city.district_id = district.district_id
WHERE city.user_id = ?
ORDER BY city.city_name";

$q = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($q, "i", $user_id);
mysqli_stmt_execute($q);

$result = mysqli_stmt_get_result($q);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?>

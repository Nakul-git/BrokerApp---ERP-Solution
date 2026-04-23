<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();

function has_state_column($con, $column_name) {
    $safe = mysqli_real_escape_string($con, $column_name);
    $q = mysqli_query($con, "SHOW COLUMNS FROM states LIKE '{$safe}'");
    return $q && mysqli_num_rows($q) > 0;
}

$fields = ["id", "state_name"];
$optional = ["state_capital", "state_area", "state_type", "state_code_char", "state_code_digit"];

foreach ($optional as $column) {
    if (has_state_column($con, $column)) {
        $fields[] = $column;
    }
}

$sql = "SELECT " . implode(",", $fields) . " FROM states WHERE user_id=? ORDER BY state_name";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?>

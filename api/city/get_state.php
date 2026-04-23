<?php
header("Content-Type: application/json");
require "../session.php";
require_once '../master_scope.php';

$user_id = get_master_scope_user_id();

$q = mysqli_prepare($con,
"SELECT id AS state_id, state_name
 FROM states
 WHERE user_id=?
 ORDER BY state_name");

mysqli_stmt_bind_param($q,"i",$user_id);
mysqli_stmt_execute($q);

$res = mysqli_stmt_get_result($q);

$data=[];

while($row=mysqli_fetch_assoc($res)){
    $data[]=$row;
}

echo json_encode($data);
?>

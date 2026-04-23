<?php
header("Content-Type: application/json");
require "../session.php";

$user_id = $_SESSION['user_id'];

$q=mysqli_prepare($con,
"SELECT IFNULL(MAX(entry_no),0)+1 AS next_no
 FROM sales_master
 WHERE user_id=?");

mysqli_stmt_bind_param($q,"i",$user_id);
mysqli_stmt_execute($q);

$res=mysqli_stmt_get_result($q);
$row=mysqli_fetch_assoc($res);

echo json_encode($row);
?>

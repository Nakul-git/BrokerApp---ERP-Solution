<?php
header("Content-Type: application/json");
require "../session.php";

$entry_no=$_GET['entry_no'];
$user_id=$_SESSION['user_id'];

$q=mysqli_prepare($con,
"SELECT entry_no FROM sales_master
 WHERE entry_no=? AND user_id=?");

mysqli_stmt_bind_param($q,"ii",$entry_no,$user_id);
mysqli_stmt_execute($q);

$res=mysqli_stmt_get_result($q);

echo json_encode([
 "exists"=>mysqli_num_rows($res)>0
]);
?>

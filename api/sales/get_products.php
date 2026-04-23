<?php
header("Content-Type: application/json");
require "../session.php";

$user_id=$_SESSION['user_id'];

$q=mysqli_prepare($con,
"SELECT product_id,product_name,sales_rate
 FROM product
 WHERE user_id=?");

mysqli_stmt_bind_param($q,"i",$user_id);
mysqli_stmt_execute($q);

$res=mysqli_stmt_get_result($q);

$data=[];
while($r=mysqli_fetch_assoc($res)) $data[]=$r;

echo json_encode($data);
?>

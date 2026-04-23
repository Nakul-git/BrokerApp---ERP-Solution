<?php
header("Content-Type: application/json");
require "../session.php";

$user_id=$_SESSION['user_id'];

$q=mysqli_prepare($con,"
SELECT s.entry_no,s.entry_date,p.party_name
FROM sales_master s
LEFT JOIN party p ON s.party_id=p.party_id
WHERE s.user_id=?
ORDER BY s.entry_no DESC
");

mysqli_stmt_bind_param($q,"i",$user_id);
mysqli_stmt_execute($q);

$res=mysqli_stmt_get_result($q);

$data=[];
while($r=mysqli_fetch_assoc($res))$data[]=$r;

echo json_encode($data);
?>

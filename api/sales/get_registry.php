<?php
header("Content-Type: application/json");
require "../session.php";

$user_id=$_SESSION['user_id'];

$from=$_GET['from'];
$to=$_GET['to'];

$sql="
SELECT s.entry_no,s.entry_date,
s.grand_total,p.party_name
FROM sales_master s
LEFT JOIN party p ON s.party_id=p.party_id
WHERE s.user_id=?
AND s.entry_date BETWEEN ? AND ?
";

if(isset($_GET['party_id'])){
$sql.=" AND s.party_id=?";
}

$q=mysqli_prepare($con,$sql);

if(isset($_GET['party_id'])){
$party=$_GET['party_id'];
mysqli_stmt_bind_param($q,"issi",
$user_id,$from,$to,$party);
}else{
mysqli_stmt_bind_param($q,"iss",
$user_id,$from,$to);
}

mysqli_stmt_execute($q);
$res=mysqli_stmt_get_result($q);

$data=[];
while($r=mysqli_fetch_assoc($res))$data[]=$r;

echo json_encode($data);
?>

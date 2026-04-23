<?php
require "../session.php";

$data=json_decode(file_get_contents("php://input"),true);

$user_id=$_SESSION['user_id'];

$entry_no   =$data['entry_no'];
$entry_date =$data['entry_date'];
$party_id   =$data['party_id'];
$remark     =$data['remark'];
$grand_total=$data['grand_total'];
$payment    =$data['payment_mode'];

mysqli_begin_transaction($con);

try{

$q=mysqli_prepare($con,"
INSERT INTO sales_master
(entry_no,entry_date,party_id,remark,
grand_total,payment_mode,user_id)
VALUES (?,?,?,?,?,?,?)
");

mysqli_stmt_bind_param($q,"isisssi",
$entry_no,$entry_date,$party_id,
$remark,$grand_total,$payment,$user_id);

mysqli_stmt_execute($q);

/* ITEMS */
foreach($data['items'] as $i){

$q=mysqli_prepare($con,"
INSERT INTO sales_items
(entry_no,product_name,quantity,
rate,amount,user_id)
VALUES (?,?,?,?,?,?)
");

mysqli_stmt_bind_param($q,"isiddi",
$entry_no,
$i['product_name'],
$i['quantity'],
$i['rate'],
$i['amount'],
$user_id);

mysqli_stmt_execute($q);
}

mysqli_commit($con);

echo json_encode(["success"=>true]);

}catch(Exception $e){

mysqli_rollback($con);
echo json_encode(["success"=>false]);
}
?>

<?php
require_once "../session.php";
$entry_no = $_GET['entry_no'];
$user_id=$_SESSION['user_id'];

$bill = mysqli_fetch_assoc(
    mysqli_query($con,"
    SELECT sm.*, p.party_name
    FROM sales_master sm
    JOIN party p ON p.party_id = sm.party_id
    WHERE sm.entry_no=$entry_no AND sm.user_id=$user_id")
);

$items = [];
$res = mysqli_query($con,"SELECT * FROM sales_items WHERE entry_no=$entry_no");
while($r=mysqli_fetch_assoc($res)) $items[]=$r;

echo json_encode(["bill"=>$bill,"items"=>$items]);

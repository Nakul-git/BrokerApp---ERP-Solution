<?php
header("Content-Type: application/json");
require "../session.php";

$entry_no = $_POST['entry_no'];
$user_id  = $_SESSION['user_id'];

mysqli_begin_transaction($con);

try{

/* DELETE ITEMS FIRST */
$q = mysqli_prepare($con,"
DELETE FROM sales_items
WHERE entry_no=? AND user_id=?
");

mysqli_stmt_bind_param($q,"ii",$entry_no,$user_id);
mysqli_stmt_execute($q);

/* DELETE MASTER */
$q = mysqli_prepare($con,"
DELETE FROM sales_master
WHERE entry_no=? AND user_id=?
");

mysqli_stmt_bind_param($q,"ii",$entry_no,$user_id);
mysqli_stmt_execute($q);

mysqli_commit($con);

echo json_encode([
    "status"=>"success",
    "message"=>"Bill deleted"
]);

}catch(Exception $e){

mysqli_rollback($con);

echo json_encode([
    "status"=>"error",
    "message"=>"Delete failed"
]);
}
?>

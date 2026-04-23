<?php
header("Content-Type: application/json");
require "../session.php";

$res = mysqli_query(
    $con_company,
    "SELECT *
     FROM company_master
     ORDER BY company_name"
);

$data=[];

while($r=mysqli_fetch_assoc($res)){
    $data[]=$r;
}

echo json_encode($data);
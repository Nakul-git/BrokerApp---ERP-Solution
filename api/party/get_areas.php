<?php
require_once "../session.php";
header("Content-Type: application/json");

$city_id = intval($_GET['city_id'] ?? 0);

if(!$city_id){
    echo json_encode([]);
    exit;
}

$result = mysqli_query($con, "SELECT area_id, name FROM area WHERE city_id = $city_id ORDER BY name");

$areas = [];
while($row = mysqli_fetch_assoc($result)){
    $areas[] = $row;
}

echo json_encode($areas);
?>

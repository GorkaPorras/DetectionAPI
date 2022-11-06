<?php

include("../db.php");

$date1 = $_POST['data1']; 
$date2 = $_POST['data2'];

$link = connect();  // Conectar a la base de datos
$allObjectsTypeAndSize = getAllObjectsTypeAndSizeByDate($link,$date1,$date2);

$typeArray = array();
$typeCountArray = array();

while ($row = mysqli_fetch_assoc($allObjectsTypeAndSize)) {
    $typeArray[] = $row['object_type'];
    $typeCountArray[] = $row['count(object_type)'];
}
$objecttypeJson = json_encode($typeArray);
$objecttypeCountJson = json_encode($typeCountArray);

echo $objecttypeJson.'+'.$objecttypeCountJson; //return

disconnect($link) ; //Desconectar BD
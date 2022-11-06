<?php

include("../db.php");

$date1 = $_POST['data1']; 
$date2 = $_POST['data2'];

$link = connect();  // Conectar a la base de datos
$ObjectsSize = getObjectsByDate($link,$date1,$date2);

$result = $ObjectsSize->fetch_array();
$size = intval($result[0]);

echo $size; //return

disconnect($link); //Descoenctar DB
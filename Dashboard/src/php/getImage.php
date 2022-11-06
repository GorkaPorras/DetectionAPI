<?php

include("../db.php");

$date1 = strtotime($_POST['data1']); //Convert to human-readable date
$date2 = strtotime($_POST['data2']);

$link = connect();  // Conectar a la base de datos
$resultset = getImagesByDate($link,$date1,$date2);

    $withOutObjects = 0;
    $witObjects = 0;
    
    while ($images = mysqli_fetch_assoc($resultset)) {

        $imgObjects = getImagesObjectsSize($link, (int)$images['image_id']);

        if ($imgObjects == 0) {
            $withOutObjects++;
        } else {
            $witObjects++;
        }
        $a=$images['image'];
    }


echo $withOutObjects.','.$witObjects; //return

disconnect($link); //Desconectar Db

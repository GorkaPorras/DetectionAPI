<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0">
    <script src="https://maps.googleapis.com/maps/api/js?key=//KEY_API//" async defer></script>

    <link rel="stylesheet" href="CSS/maps.css">
    <script src="JS/maps.js"></script>
    <script src="JS/menu.js"></script>
    <link href="CSS/menu.css" rel="stylesheet">

    <title>Maps API</title>

    <?php

    include("db.php");
    $link = connect();
    
    ?>
</head>

<?php

//Inicializar mapa según las variables get de la url
if (empty($_GET["image_id"]) &&   empty($_GET["date1"])) {

    echo '<body onload="initMap(' . $_GET["lat"] . ',' . $_GET["long"] . ')"> '; #mostrar un objeto en el mapa
} else if (isset($_GET["image_id"]) &&   empty($_GET["date1"])) {

    $result = getObjectsGeo($link, $_GET["image_id"]);
    $geoarray = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $geoarray[] = $row;
    }
    if ($geoarray != null) {

        $geo = json_encode($geoarray);
        echo "<body onload='initMap2(  $geo  )'> "; #mostrar todos los objetos en el mapa
    } else {

        echo "<body onload='initMapAllObjects(  $geo  )'> "; #mostrar todos los objetos en el mapa
    }
} else {

    $result2 =  getObjectsLocationByDate($link, $_GET["date1"], $_GET["date2"]);
    $geoarray = array();
    while ($row = mysqli_fetch_assoc($result2)) {
        $geoarray[] = $row;
    }
    $geo = json_encode($geoarray);
    echo "<body onload='initMapAllObjects(  $geo  )'> "; #mostrar todos los objetos en el mapa
}

?>

<div id="mySidepanel" class="sidepanel">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
    <a href="index.php">Home</a>
    <a href="#" onclick="history.back ()">Back</a>
    <a href="../" onclick="javascript:event.target.port=5001">API</a>

</div>

<div class='menu'>
    <button class="openbtn btn-flotante" onclick="openNav()">☰</button>
</div>
<div id="map"> </div>

<?php disconnect($link) ?>

</body>

</html>

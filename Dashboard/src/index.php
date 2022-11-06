<!DOCTYPE html>
<html>

<head>

    <meta charset=UTF-8>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="JS/grafico.js"></script>
    <script src="JS/index.js"></script>
    <link href="CSS/index.css" rel="stylesheet">
    <script src="JS/menu.js"></script>
    <link href="CSS/menu.css" rel="stylesheet">

    <?php
    include("db.php");
    $link = connect();  // Conectar a la base de datos

    //Calcular cuantas imagenes tienen objetos
    $resultset = getImages($link);
    $withOutObjects = 0;
    $witObjects = 0;
    while ($images = mysqli_fetch_assoc($resultset)) {

        $imgObjects = getImagesObjectsSize($link, (int)$images['image_id']);
        if ($imgObjects == 0) {
            $withOutObjects++;
        } else {
            $witObjects++;
        }
    }

    //Calcular el número de objetos diferentes y la cantidad
    $allObjectsTypeAndSize = getAllObjectsTypeAndSize($link);
    $typeArray = array();
    $typeCountArray = array();
    while ($row = mysqli_fetch_assoc($allObjectsTypeAndSize)) {
        $typeArray[] = $row['object_type'];
        $typeCountArray[] = $row['count(object_type)'];
    }
    $objecttypeJson = json_encode($typeArray);
    $objecttypeCountJson = json_encode($typeCountArray);

    #La última imagen 
    $lastImageResult = getLastImage($link);
    $lastImage = mysqli_fetch_assoc($lastImageResult)

    ?>

    <title>Prueba de PHP</title>

</head>

<?php echo "<body onload='graficaPastel($withOutObjects,$witObjects),graficaPastel2($objecttypeJson,$objecttypeCountJson)'>" ?>

    <div id="mySidepanel" class="sidepanel">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
        <a href="#">Home</a>
        <a href="image.php?date1=&%20date2=">All Images</a>
        <a href="maps.php?date1=2000-06-17& date2=2100-06-17"> All Objects Location</a>
        <a href="" onclick="javascript:event.target.port=5001">API</a>

    </div>

    <div class='menu'>
        <button class=" btn-flotante" onclick="openNav()">☰</button>
    </div>

    <div class='container'>

        <div class="row justify-content-center">
            <h1 class="text-center">Dashboard</h1>
        </div>

        <div class='row '>
            <div class='col-md-5 shadow  bg-light border m-1 ' style=' padding: 1rem !important;'>
                <div class='col text-center '>
                    <h6>Objects in Image</h6>
                    <input type="date" id="date1" name="date1" onchange=actualizarGrafico1(1,this.value)>
                    <input type="date" id="date2" name="date2" onchange=actualizarGrafico1(2,this.value)>
                </div>
                <div class='row text-center' onclick="redirectImagePage()" style="width:80% ;margin:auto; ">
                    <canvas id="oilChart"></canvas>
                    <p class='cursor'>See images</p>
                </div>
            </div>
            <div class='col-md-5 shadow  bg-light border m-1' style='  padding: 1rem !important;'>
                <div class='col text-center '>
                    <h6>Objects detected</h6>
                    <input type="date" id="date1" name="date1" onchange=actualizarGrafico2(1,this.value)>
                    <input type="date" id="date2" name="date2" onchange=actualizarGrafico2(2,this.value)>
                </div>
                <div class='row' style="width:90% ;  margin:auto;">
                    <canvas id="oilChart2"></canvas>
                </div>
            </div>
        </div>

        <div class='row'>
            <div class='col-md-5 shadow  bg-light border m-1 cursor' style="padding-top:1em" onclick="getObjects(<?php echo $lastImage['image_id']; ?>,'<?php echo $lastImage['image']; ?>.<?php echo $lastImage['type']; ?>')">
                <div class='row text-center'>
                    <h6>The last image</h6>
                </div>
                <div class='row' style="max-width:415px;max-height:285px;margin:auto;">
                    <img class="img-fluid " align="middle" src="static/UPLOAD_FOLDER/img/<?php echo $lastImage['image']; ?>.<?php echo $lastImage['type']; ?>" style="max-width:415px;max-height:285px">
                </div>
                <div class='row' style="padding-top:10px">
                    <div class='col'>
                        <h6>id: <?php echo $lastImage['image_id']; ?></h6>
                        <h6>name: <?php echo $lastImage['image']; ?>.<?php echo $lastImage['type']; ?></h6>
                        <h6>size: <?php echo $lastImage['width']; ?>x<?php echo $lastImage['height']; ?></h6>
                        <h6>TimeStamp: <?php echo date('Y/d/m H:i:s', $lastImage['image']); ?></h6>
                    </div>
                    <div class='col'>
                        <h6 class='objects'>Objects: <?php echo getImagesObjectsSize($link, (int)$lastImage['image_id']); ?></h6>
                    </div>
                </div>
            </div>
            <div class='col-md-5 shadow  bg-light border m-1 text-center '>
                <div class='col  ' style="padding-top:1em">
                    <h6> Objects in Map</h6>
                    <input type="date" id="dateMaps1" name="date1" onchange="dataChange(1,this.value)">
                    <input type="date" id="dateMaps2" name="date2" onchange="dataChange(2,this.value)">
                </div>
                <div class='row' id='maps' onclick="redirectMapsPage()">
                    <img src="./img/marcador.png" alt="image">
                    <p id='obj'>Objects: <?php echo getAllObjectsSize($link); ?></p>
                    <p>Open map</p>
                </div>
            </div>

        
        </div>
    </div>

</body>
<html>
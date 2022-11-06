<!DOCTYPE html>
<html>

<head>
    <meta charset=UTF-8>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
    <script src="JS/script.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="CSS/objects.css" rel="stylesheet">
    <script src="JS/objects.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js"></script>
    <script src="JS/grafico.js"></script>

    <script src="JS/menu.js"></script>
    <link href="CSS/menu.css" rel="stylesheet">

    <?php

    include("db.php");

    $link = connect();
    $resultset = getObjects($link, $_GET['image_id']);

    //Dibujar las detecciones en la imagen
    dibujar_detecciones((string)$_GET['image'], $resultset);

    //Calcular el número de objetos diferentes y la cantidad
    mysqli_data_seek($resultset, 0);//Empezar desde el início
    $object_typeCount = getObjectsTypeAndSize($link, $_GET['image_id']);
    $typeArray = array();
    $typeCountArray = array();

    while ($row = mysqli_fetch_assoc($object_typeCount)) {
        $typeArray[] = $row['object_type'];
        $typeCountArray[] = $row['count(object_type)'];
    }
    $objecttypeJson = json_encode($typeArray);
    $objecttypeCountJson = json_encode($typeCountArray);

    ?>

    <title>Prueba de PHP</title>
</head>

<?php echo "<body onload='interactiva(),modal(),grafico( $objecttypeJson,$objecttypeCountJson )'>"; ?>

    <div id="mySidepanel" class="sidepanel">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
        <a href="index.php">Home</a>
        <a href="#" onclick = "history.back ()">Back</a>
        <a href="../" onclick="javascript:event.target.port=5001">API</a>

    </div>

    <div class='menu'>
        <button class="openbtn btn-flotante" onclick="openNav()">☰</button>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <h1 class="text-center">Image</h1>
        </div>
        <div class='row'>
            <div class='col-md-6 text-center' >
                <img class='img-fluid' id="myImg" src="./img/detections.png" alt="image"style=width:100%;height:100%;max-width:600px;max-height:350px >
                <div id="myModal" class="modal">
                    <span class="close">&times;</span>
                    <img class="modal-content" id="img01">
                    <div id="caption"></div>
                </div>
            </div>
            <div class='col-md-4'>
                <canvas id="densityChart" width="100%" height="90%"></canvas>
            </div>
            <div class='col-md-2  maps'>
                <img src="./img/marcador.png" alt="image" style="width:100%;max-width:50px;" onclick="openMapsObjects(<?php echo $_GET['image_id']; ?>)">
            </div>
        </div>
        <div class="row justify-content-center">
            <h1 class="text-center">Objects list</h1>
        </div>
        <select id="object_type" onchange="verIndex()">

            <?php

            echo "<option value='type'selected>Todo</option>";
            $object_type = getObjectsType($link, $_GET['image_id']);
            while ($type = mysqli_fetch_assoc($object_type)) {

                echo "<option value='type'>" . $type['object_type'] . "</option>";
            }

            ?>

        </select>

        <?php

        while ($objects = mysqli_fetch_assoc($resultset)) {
        ?>
            <div class="row border border-1 <?php echo $objects['object_type']; ?> object" style="margin-top: 20px; background: #f5f5f5;">
                <div class="col-md-4 col-xs-12  graficos text-center">
                    <h4> <?php echo $objects['object_type']; ?></h4>
                    <div class='arco'>
                        <?php echo $objects['confidence']; ?>
                    </div>
                    <h6> Confidence</h6>
                </div>
                <div class="col-md-4 col-xs-12 ">
                    <h4 class='text-center'> Descrption</h4>
                    <h6>id: <?php echo $objects['object_id']; ?></h6>
                    <h6>bb_x: <?php echo $objects['bb_x']; ?> px</h6>
                    <h6>bb_y: <?php echo $objects['bb_y']; ?> px</h6>
                    <h6>Size: <?php echo $objects['width']; ?> x <?php echo $objects['heigth']; ?> px</h6>
                    <h6>Timestamp: <?php echo $objects['timestamp']; ?></h6>
                </div>
                <div class="col-md-4 col-xs-12 maps text-center" onclick="openMaps('<?php echo $objects['lat']; ?>','<?php echo $objects['long']; ?>')">
                    <h4>Location</h4>
                    <img src="./img/marcador.png" alt="image" style="width:100%;max-width:50px">
                    <h6><?php echo $objects['long']; ?> , <?php echo $objects['lat']; ?></h6>
                </div>
            </div>
        <?php } ?>
    </div>
    </div>
    <?php disconnect($link) ?>
</body>

</html>

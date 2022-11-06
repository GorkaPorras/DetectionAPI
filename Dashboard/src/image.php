<!DOCTYPE html>
<html>

<head>
    <meta charset=UTF-8>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="CSS/images.css">
    <script src="JS/image.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="JS/menu.js"></script>
    <link href="CSS/menu.css" rel="stylesheet">

    <?php
    include("db.php");
    ?>

    <title>Prueba de PHP</title>
</head>

<body>

    <div id="mySidepanel" class="sidepanel">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
        <a href="index.php">Home</a>
        <a href="#" onclick="history.back ()">Back</a>
        <a href="../" onclick="javascript:event.target.port=5001">API</a>

    </div>
    <div class='menu'>
        <button class="openbtn btn-flotante" onclick="openNav()">☰</button>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <h1 class="text-center">Images</h1>
        </div>

        <?php
        $link = connect();
        $date1 = $_GET['date1'];
        $date2 = $_GET['date2'];

        if ($date1 == "" && $date2 == "") {

            $resultset = getImages($link);
        } else {

            if ($date1 == "") {
                $date1 = '2000-06-17';
            } else {
                $date2 = '2100-06-17';
            }
            $resultset = getImagesByDate($link, strtotime($date1), strtotime($date2));
        }

        ?>

        <select id="img_type" onchange="hiddenDiv()">
            <option value='all' selected>All</option>
            <option value='with'>with Objects</option>
            <option value='without'>without Objects</option>
        </select>

        <?php while ($images = mysqli_fetch_assoc($resultset)) { ?>
            <div class="row border border-1 hoverDiv" style="margin-top: 20px;" onclick="getObjects(<?php echo $images['image_id']; ?>,'<?php echo $images['image']; ?>.<?php echo $images['type']; ?>')">
                <div class="col-md-4 col-xs-12 text-center" style="max-width:415px;max-height:235px">
                    <img class="img-fluid " src="static/UPLOAD_FOLDER/img/<?php echo $images['image']; ?>.<?php echo $images['type']; ?>" style="width:100%;height:100%">
                </div>
                <div class="col-md-4 col-xs-12" style="margin: auto;">
                    <h6>id: <?php echo $images['image_id']; ?></h6>
                    <h6>name: <?php echo $images['image']; ?>.<?php echo $images['type']; ?></h6>
                    <h6>size: <?php echo $images['width']; ?>x<?php echo $images['height']; ?></h6>
                    <h6>TimeStamp: <?php echo date('Y/d/m H:i:s', $images['image']); ?></h6>
                </div>
                <div class="col-md-4 col-xs-12" style="margin: auto;">
                    <h6 class='objects'>Objects: <?php echo getImagesObjectsSize($link, (int)$images['image_id']); ?></h6>
                </div>
                <div class="col-md-4 col-xs-12">
                </div>
            </div>
        <?php } ?>
    </div>

</body>

<?php disconnect($link) ?>

</html>
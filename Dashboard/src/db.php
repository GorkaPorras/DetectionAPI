
 <?php

   //Configurar la conexión de la base de datos
   $host = "mysql"; // Dirección o IP del servidor MySQL
   $puerto = "3306"; // Puerto del servidor MySQL
   $usuario = "root"; // Nombre de usuario del servidor MySQL
   $contrasena = "root"; // Contraseña del usuario
   $baseDeDatos = "Detections"; // Nombre de la base de datos

   //Conectar a la base de datos
   function connect()
   {
      global $host, $puerto, $usuario, $contrasena, $baseDeDatos;

      if (!($link = mysqli_connect($host . ":" . $puerto, $usuario, $contrasena))) {
         echo "Error conectando a la base de datos.<br>";
         exit();
      } else {
         #echo "Listo, estamos conectados.<br>";
      }
      if (!mysqli_select_db($link, $baseDeDatos)) {
         echo "Error, no existe la base de datos o esta vacía. Ingresa una imagen en la API para poder acceder al Dashboard<br>";
         echo "<a href='' onclick='javascript:event.target.port=5001'>API</a>";
         exit();
      } else {
         #echo "Obtuvimos la base de datos $baseDeDatos sin problema.<br>";
      }
      return $link;
   }

   //Descoenctar la base de datos
   //Parámetro1:Conexión de la BD
   function disconnect($link)
   {
      mysqli_close($link);
   }

   //Devuelve todas las imágenes de la BD
   //Parámetro1:Conexión de la BD
   function getImages($link)
   {
      $sql_query = "SELECT * FROM Images";
      $resultset = mysqli_query($link, $sql_query) or die("error base de datos:" . mysqli_error($link));
      return $resultset;
   }

   //Devuelve la cantidad de objetos de la base de datos
   //Parámetro1:Conexión de la BD
   function getAllObjectsSize($link)
   {
      $sql_query = "SELECT count(*) FROM Objects";
      $resultset = mysqli_query($link, $sql_query) or die("error base de datos:" . mysqli_error($link));
      $result = $resultset->fetch_array();
      $quantity = intval($result[0]);
      return $quantity;
   }

   //Devuelve la cantidad de objetos de una imagen de la base de datos
   //Parámetro1:Conexión de la BD
   //Parámetro2: id de una imagen
   function getImagesObjectsSize($link, $imageID)
   {
      $sql_query = "SELECT count(*) FROM Objects where image_id=$imageID";

      $resultset = mysqli_query($link, $sql_query) or die("error base de datos:" . mysqli_error($link));
      $result = $resultset->fetch_array();
      $quantity = intval($result[0]);

      return $quantity;
   }

   //Devuelve todos los objetos de una imagen
   //Parámetro1:Conexión de la BD
   //Parámetro2: id de una imagen
   function getObjects($link, $imageID)
   {
      $sql_query = "SELECT * FROM Objects where image_id=$imageID";
      $resultset = mysqli_query($link, $sql_query) or die("error base de datos:" . mysqli_error($link));
      return $resultset;
   }

   //Dibujar detecciones en la imagen y guardarla
   //Parámetro1:Nombre y tipo de la imagen (imagen.png)
   //Parámetro2: Detecciones
   function dibujar_detecciones($image, $resultset)
   {
      $type = explode(".", $image)[2];
      if (strcmp($type, 'png') == 0) {
         $im = imagecreatefrompng("./static/UPLOAD_FOLDER/img/" . $image);
      } elseif (strcmp($type, 'jpeg') == 0) {
         $im = imagecreatefromjpeg("./static/UPLOAD_FOLDER/img/" . $image);
      } elseif (strcmp($type, 'jpg') == 0) {
         $im = imagecreatefromjpeg("./static/UPLOAD_FOLDER/img/" . $image);
      }
      if (!$im) {
         echo '<script language="javascript">alert("Error cargando image.jpeg");</script>';
      } else {
         while ($objects = mysqli_fetch_assoc($resultset)) {
            $bb_x = $objects['bb_x'];
            $bb_y = $objects['bb_y'];
            $width = $objects['width'];
            $heigth = $objects['heigth'];

            $color = imagecolorallocate($im, 255, 255, 255);
            $x1 = $bb_x - ($width / 2);
            $y1 = $bb_y + ($heigth / 2);

            $x2 = $bb_x + ($width / 2);
            $y2 = $bb_y - ($heigth / 2);

            //imagesetthickness(3);//Establecer el grosor de línea

            $text = $objects['object_type'] . " " . $objects['confidence'];
            imagestring($im, 3, $x1, $y2 - 15, $text, $color);
            imagerectangle($im, $x1, $y1, $x2, $y2, $color);
         }

         $source = "./img/detections.png";
         if (strcmp($type, 'png') == 0) {
            imagepng($im, $source);
         } elseif (strcmp($type, 'jpeg') == 0) {
            imagepng($im, $source);
         } elseif (strcmp($type, 'jpg') == 0) {
            imagepng($im, $source);
         }
      }
   }

   //Devuelve la geolocalización de los objetos de una imagen
   //Parámetro1:Conexión de la BD
   //Parámetro2: id de una imagen
   function getObjectsGeo($link, $imageID)
   {
      $sql_query = "SELECT lat,`long` FROM Objects where image_id=$imageID";
      $resultset = mysqli_query($link, $sql_query) or die("error base de datos:" . mysqli_error($link));
      return $resultset;
   }

   //Devuelve los diferentes tipos de objetos detectados en una imagen
   //Parámetro1:Conexión de la BD
   //Parámetro2: id de una imagen
   function getObjectsType($link, $imageID)
   {
      $sql_query = "SELECT distinct object_type FROM Objects where image_id=$imageID";
      $resultset = mysqli_query($link, $sql_query) or die("error base de datos:" . mysqli_error($link));
      return $resultset;
   }

   //Devuelve los diferentes tipos de objetos y la cantidad detectados en una imagen
   //Parámetro1:Conexión de la BD
   //Parámetro2: id de una imagen
   function getObjectsTypeAndSize($link, $imageID)
   {
      $sql_query = "SELECT  object_type , count(object_type) FROM Objects where image_id=$imageID group by object_type";
      $resultset = mysqli_query($link, $sql_query) or die("error base de datos:" . mysqli_error($link));
      return $resultset;
   }

   //Devuelve las imágenes que están dentro de la fecha
   //Parámetro1:Conexión de la BD
   //Parámetro2: fecha de inicio
   //Parámetro3: fecha final
   function getImagesByDate($link, $date1, $date2)
   {
      $sql_query = "SELECT * FROM Images WHERE image>=$date1 and image<=$date2";
      $resultset = mysqli_query($link, $sql_query) or die("error base de datos:" . mysqli_error($link));
      return $resultset;
   }


   //Devuelve todos los diferentes tipos de objetos y la cantidad detectados
   //Parámetro1:Conexión de la BD
   function getAllObjectsTypeAndSize($link)
   {
      $sql_query = "SELECT  object_type , count(object_type) FROM Objects GROUP BY object_type";
      $resultset = mysqli_query($link, $sql_query) or die("error base de datos:" . mysqli_error($link));
      return $resultset;
   }

   //Devuelve todos los diferentes tipos de objetos y la cantidad detectados dentro de una fecha
   //Parámetro1:Conexión de la BD
   //Parámetro2: fecha de inicio
   //Parámetro3: fecha final
   function getAllObjectsTypeAndSizeByDate($link, $date1, $date2)
   {
      $sql_query = "SELECT  object_type , count(object_type) FROM Objects WHERE SUBSTRING_INDEX(timestamp, ' ', 1)>='$date1' and SUBSTRING_INDEX(timestamp, ' ', 1)<='$date2' GROUP BY object_type";
      $resultset = mysqli_query($link, $sql_query) or die("error base de datos:" . mysqli_error($link));
      return $resultset;
   }

   //Devuelve la geolocalización de todos los objetos que están dentro de una fecha
   //Parámetro1:Conexión de la BD
   //Parámetro2: fecha de inicio
   //Parámetro3: fecha final
   function getObjectsLocationByDate($link, $date1, $date2)
   {
      $sql_query = "SELECT  lat , `long` FROM Objects WHERE SUBSTRING_INDEX(timestamp, ' ', 1)>='$date1' and SUBSTRING_INDEX(timestamp, ' ', 1)<='$date2'";
      $resultset = mysqli_query($link, $sql_query) or die("error base de datos:" . mysqli_error($link));
      return $resultset;
   }

   //Devuelve la última imagen
   //Parámetro1:Conexión de la BD
   function getLastImage($link)
   {
      $sql_query = "SELECT * FROM Images where image_id=(select max(image_id) from Images)";
      $resultset = mysqli_query($link, $sql_query) or die("error base de datos:" . mysqli_error($link));
      return $resultset;
   }

   //Devuelve todos los objetos que están dentro de una fecha
   //Parámetro1:Conexión de la BD
   //Parámetro2: fecha de inicio
   //Parámetro3: fecha final
   function getObjectsByDate($link, $date1, $date2)
   {

      $sql_query = "SELECT  count(*) FROM Objects WHERE SUBSTRING_INDEX(timestamp, ' ', 1)>='$date1' and SUBSTRING_INDEX(timestamp, ' ', 1)<='$date2'";
      $resultset = mysqli_query($link, $sql_query) or die("error base de datos:" . mysqli_error($link));
      return $resultset;
   }

   ?>
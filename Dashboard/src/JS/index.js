//Redireccionar a la página objects.php y mostrar las imágenes que están dentro de la fecha seleccionada
//index.php
function redirectImagePage() {

  var date1 = document.getElementById('date1').value;
  var date2 = document.getElementById('date2').value;

  window.location = "image.php?date1=" + date1 + '& date2=' + date2;
}

//Mostrar cuantos objetos tienen las imágenes que están dentro de la fecha seleccionada
//parámetros: number(para saber que fecha se ha actualizado) y date (2000-06-11)
//index.php
var date1 = '2000-06-17';
var date2 = '2100-06-17';
function dataChange(number, date) {
  if (number == 1) {
    date1 = date;
  } else {
    date2 = date;
  }

  //Ejecutar PHP
  $.ajax({
    method: "POST",
    url: "php/getObjectsSize.php",
    data: {
      'data1': date1,
      'data2': date2
    }
  }).done(function (date) {
    console.log("success");
    document.getElementById('obj').innerHTML = 'Objects: ' + date; //Actualizar valor

  }).fail(function () {
    console.log("error");
  });
}

//Redireccionar a la página maps.php y mostrar objetos que están dentro de la fecha seleccionada
//index.php
function redirectMapsPage() {

  var date1 = document.getElementById('dateMaps1').value;
  var date2 = document.getElementById('dateMaps2').value;

  if (date1 == '' && date2 == '') {
    date1 = '2000-06-17';
    date2 = '2100-06-17';
  } else if (date2 == '') {
    date2 = '2100-06-17';
  } else if (date1 == '') {
    date1 = '2000-06-17';
  }
  window.location = "maps.php?date1=" + date1 + '& date2=' + date2;
}

//Mostrar cuantos objetos tiene una imagen
//Parametros:id de una imagen y nombre de la imagen (imagen.jpg)
//index.php
function getObjects(image_id, image) {
  window.location = "objects.php?image_id=" + image_id + '&' + 'image=' + image;
}
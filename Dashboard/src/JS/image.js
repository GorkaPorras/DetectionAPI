//Mostrar objetos de una imagen en 'objects.php'
//Parametros: id de la imagen y el nombre de la imagen (imagen.png)
//image.php
function getObjects(image_id, image) {
  window.location = "objects.php?image_id=" + image_id + '&' + 'image=' + image;
}

//Filtro de imagenes
//image.php
function hiddenDiv() {

  var select = document.getElementById("img_type");
  var opcion = select.selectedIndex;

  list = document.getElementsByClassName('hoverDiv');

  if (select.options[opcion].value != 'all') {

    for (var i = 0; i < list.length; i++) {
      var objects = list[i].getElementsByClassName('objects')[0].innerHTML;
      const split = objects.split(' ')

      if (select.options[opcion].value == 'without') {
        if (split[1] == '0') {
          list[i].style.display = "flex";

        } else {
          list[i].style.display = "none";
        }
      } else {
        if (split[1] == '0') {
          list[i].style.display = "none";
        } else {
          list[i].style.display = "flex";
        }
      }
    }
  } else {
    for (var i = 0; i < list.length; i++)
      list[i].style.display = "flex";
  }
}
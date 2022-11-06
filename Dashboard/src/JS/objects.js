//Zoom en la imagen de detecciones al hacer click
//objects.php
function modal() {
    var modal = document.getElementById("myModal");

    // Get the image and insert it inside the modal - use its "alt" text as a caption
    var img = document.getElementById("myImg");
    var modalImg = document.getElementById("img01");
    var captionText = document.getElementById("caption");

    if (img != null) {
        img.onclick = function () {
            modal.style.display = "block";
            modalImg.src = this.src;
            captionText.innerHTML = this.alt;
        }

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function () {
            modal.style.display = "none";
        }
    }
};

//Mostrar en el mapa de Google maps la localización del objeto seleccionado
//Parametros: latitud y longitud de un objeto
//maps.php
function openMaps(lat, long) {
    window.location = "maps.php?lat=" + lat + '&' + 'long=' + long;
}

//Mostrar en el mapa de Google maps la localización de todos los objetos de la imagen
//Parametros: id d ela imagen
//maps.php
function openMapsObjects(image_id) {
    window.location = "maps.php?image_id=" + image_id;
}

//Filtro de los objetos
//objects.php
function verIndex() {
    var lista = document.getElementById("object_type");
    var opcion = lista.selectedIndex;
    var hidden = document.getElementsByClassName('object');
    var show = document.getElementsByClassName(lista.options[opcion].firstChild.data);

    if (lista.options[opcion].firstChild.data != 'Todo') {
        for (var i = 0; i < hidden.length; i++) {
            hidden[i].style.display = "none";
        }

        for (var i = 0; i < show.length; i++) {
            show[i].style.display = "flex";
        }
    } else {
        for (var i = 0; i < hidden.length; i++) {
            hidden[i].style.display = "flex";
        }
    }
}

//Grafico de la confianza 
//objects.php
function interactiva() {
    var valor = 0;
    var elArco = document.getElementsByClassName("arco");

    for (var i = 0; i < elArco.length; i++) {

        valor = (elArco[i].innerHTML * 180) / 100;//(elArco[i].parentNode.innerHTML);
        elArco[i].innerHTML = (100 / (180 / valor)).toFixed(2) + "%";
        elArco[i].style.backgroundImage = "radial-gradient(circle at bottom, #f5f5f5 36%, transparent 38%, transparent 65%, #f5f5f5 67%), linear-gradient(" + valor + "deg, orange 50%, #f5f5f5 50%)";
    }
}

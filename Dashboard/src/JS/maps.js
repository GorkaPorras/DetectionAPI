//Mostrar mapa de Google maps con la localización de un objeto 
//maps.php
function initMap(lat, long) {
    var map;
    var marker;

    var lat = Number(lat);
    var long = Number(long);

    map = new google.maps.Map(document.getElementById('map'), {
        center: {
            lat: lat,
            lng: long
        },
        zoom: 15,
        mapTypeId: 'hybrid',
        scaleControl: true,
        mapTypeControl: true,
        mapTypeControl: true,
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
            position: google.maps.ControlPosition.TOP_CENTER,
        },

        fullscreenControlOptions: {
            position: google.maps.ControlPosition.RIGHT_BOTTOM
        },
    });

    //Añadir marcador del objeto
    marker = new google.maps.Marker({
        position: {
            lat: lat,
            lng: long
        },
        map: map,
        title: 'Object'
    })
}

//Mostrar mapa de Google maps con la localización de todos los objetos de una imagen
//Parametro: Un JSON con la localización (latitud y longitud) de los objetos
//maps.php
function initMap2(geo) {

    var map;
    var marker;

    map = new google.maps.Map(document.getElementById('map'), {
        center: {
            lat: Number(geo[0]['lat']),
            lng: Number(geo[0]['long'])
        },
        zoom: 15,
        mapTypeId: 'hybrid',
        scaleControl: true,
        mapTypeControl: true,
        mapTypeControl: true,
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
            position: google.maps.ControlPosition.TOP_CENTER,
        },

        fullscreenControlOptions: {
            position: google.maps.ControlPosition.RIGHT_BOTTOM
        },
    });

    //Mostrar los marcadores de los objetos
    for (obj in geo) {
        marker = new google.maps.Marker({
            position: {
                lat: Number(geo[obj]['lat']),
                lng: Number(geo[obj]['long'])
            },
            map: map,
            title: 'Position obj' + obj
        });
    }
}

//Mostrar mapa de Google maps con la localización de todos los objetos recividos
//Parametro: Un JSON con la localización (latitud y longitud) de los objetos
//maps.php
function initMapAllObjects(geo) {

    var map;
    var marker;

    map = new google.maps.Map(document.getElementById('map'), {
        center: {
            lat: 0,
            lng: 0
        },
        zoom: 3,
        mapTypeId: 'hybrid',
        scaleControl: true,
        mapTypeControl: true,
        mapTypeControl: true,
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
            position: google.maps.ControlPosition.TOP_CENTER,
        },
        fullscreenControlOptions: {
            position: google.maps.ControlPosition.RIGHT_BOTTOM
        },
    });

    //Marcador de los objectos
    for (obj in geo) {
        marker = new google.maps.Marker({
            position: {
                lat: Number(geo[obj]['lat']),
                lng: Number(geo[obj]['long'])
            },
            map: map,
            title: 'Position obj' + obj
        });
    }
}


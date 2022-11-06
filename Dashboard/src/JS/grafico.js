// Mostrar histograma de cuantos objetos de cada tipo se han detectado
// objects.php
function grafico(objectType, objectCount) {

    var densityCanvas = document.getElementById("densityChart");

    Chart.defaults.global = {
        defaultFontFamily: "Lato",
        defaultFontSize: 18
    }

    var densityData = {
        label: 'Detected objects',
        data: objectCount
    };

    var barChart = new Chart(densityCanvas, {
        type: 'bar',
        data: {
            labels: objectType,
            datasets: [densityData]
        }
    });
}

// Mostrar gráfico circular 'Objects in Image'
//index.php
let pieChart;
function graficaPastel(withOutObjects, withObjects) {

    if (pieChart) {
        pieChart.destroy();
    }
    var oilCanvas = document.getElementById("oilChart");

    Chart.defaults.global = {
        defaultFontFamily: "Lato",
        defaultFontSize: 18
    }


    var oilData = {
        labels: [
            "with objects",
            "Without objects"
        ],
        datasets: [
            {
                data: [withObjects, withOutObjects],
                backgroundColor: [
                    "#84FF63",
                    "#6384FF"
                ]
            }]
    };

    pieChart = new Chart(oilCanvas, {
        type: 'pie',
        data: oilData,
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}



//Actualizar gráfico circular 'Objects in Image'
//index.php
var data1 = '2000-06-17';
var data2 = '2100-06-17';
function actualizarGrafico1(number, data) {

    if (number == 1) {
        data1 = data;
    } else {
        data2 = data;
    }
  
    $.ajax({
        method: "POST",
        url: "php/getImage.php",
        data: {
            'data1': data1,
            'data2': data2

        }
    }).done(function (data) {
        console.log("success");
        var img = data.split(',');
        graficaPastel(img[0], img[1]);


    }).fail(function () {
        console.log("error");
    });
}

// Mostrar gráfico circular 'Objects detected'
//index.php
let pieChart2;
function graficaPastel2(objectType, objectCount) {

    if (pieChart2) {
        pieChart2.destroy();
    }
    var oilCanvas = document.getElementById("oilChart2");

    Chart.defaults.global = {
        defaultFontFamily: "Lato",
        defaultFontSize: 18
    }

    var oilData = {
        labels: objectType,
        datasets: [
            {
                data: objectCount,
                backgroundColor: [
                    "#84FF63",
                    "#6384FF",
                    "#808080",
                    "#DAA520",
                    "#FFD700",
                    "#000000",
                    "#0000FF",
                    "#D2691E",
                    "#A52A2A",
                    "#FFF8DC",
                    "#00BFFF",
                ]
            }]
    };

    pieChart2 = new Chart(oilCanvas, {
        type: 'pie',
        data: oilData,
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

}

// Actualizar gráfico circular 'Objects detected'
//index.php
var grafico2Data1 = '2000-06-17';
var grafico2Data2 = '2100-06-17';
function actualizarGrafico2(number, data) {

    if (number == 1) {
        grafico2Data1 = data;
    } else {
        grafico2Data2 = data;
    }
    $.ajax({
        method: "POST",
        url: "php/getAllObjectsTypeAndSize.php",
        data: {
            'data1': grafico2Data1,
            'data2': grafico2Data2

        }
    }).done(function (data) {
        console.log("success");
        var arr = data.split('+');
        graficaPastel2(JSON.parse(arr[0]), JSON.parse(arr[1]));

    }).fail(function () {
        console.log("error");
    });
}
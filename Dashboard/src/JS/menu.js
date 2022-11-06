//Abrir menu al clickar el boton
//En todas las páginas
function openNav() {
  document.getElementById("mySidepanel").style.width = "200px";
  document.getElementsByClassName('menu')[0].style.display = 'none'
}

//Cerrar menu al clickar el boton
//En todas las páginas
function closeNav() {
  document.getElementById("mySidepanel").style.width = "0";
  document.getElementsByClassName('menu')[0].style.display = 'flex'
}





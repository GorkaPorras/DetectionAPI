# DetectionAPI
Rest API y Dashboard

Hay 3 servicios diferentes, una API, una base de datos MySQL y un Dashboard que muestra la información de la base de datos.

El acceso a la API se hace mediante el navegador. El navegador muestra una página web que sirve para enviar un archivo a un servidor (vídeo o imagen). La API envía el archivo a un servidor usando soket. Después, el servidor envía los datos usando soket en formato JSON a la API. Y la API muestra el JSON en la pantalla. Además, la API guarda la información recibida en una base de datos MySQL.

El Dasboard, sirve para mostrar datos de la BD con graficos y mapas.

Todos los servicios están preparados para que sean Dockerizados y para ejecutarlos con Docker-Compose.




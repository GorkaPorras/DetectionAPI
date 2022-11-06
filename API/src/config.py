class Config():
    DEBUG=True

    # Carpeta de subida
    IMG_UPLOAD_FOLDER='./static/UPLOAD_FOLDER/img/'
    VIDEO_UPLOAD_FOLDER='./static/UPLOAD_FOLDER/video/'

    #MySQL
    MYSQL_HOST='mysql'
    MYSQL_USER='root'
    MYSQL_PASSWORD='root'
    MYSQL_DB='Detections'
    MYSQL_PORT='3306'

    #DNN socket image (Objetos de la calle de Bilbao)
    DNN_ADDRESS_IMG='192.168.1.50'
    DNN_PORT_IMG=60001
    DETECTIONS_IMAGE='/static/img/imagenDetection.png'

    #DNN socket video (baliza)
    DNN_ADDRESS_VIDEO='192.168.1.50'
    DNN_PORT_VIDEO=59999
    DETECTIONS_VIDEO='/static/video/videoDetection.webm'

    #Servidor IP cámara (DNN baliza)
    VIDEOCAM_URL = "http://192.168.1.65:8080/video"


import mysql.connector
import cv2 
import datetime 


from functions import fileName,fileType
from config import Config

#Connectar con la base de datos
def getMysqlConnection():
    connection=mysql.connector.connect(user=Config.MYSQL_USER, 
                                host=Config.MYSQL_HOST,
                                port=Config.MYSQL_PORT, 
                                password=Config.MYSQL_PASSWORD)
   
    createDB(connection)
    return connection

#Crear DB de detecciones y tablas relacionadas con los videos si no existen
def createDB(connection):
    cursor = connection.cursor()
    cursor.execute("CREATE DATABASE IF NOT EXISTS Detections;")
    cursor.execute( "USE Detections;")

    cursor.execute( "CREATE TABLE IF NOT EXISTS Videos(video_id INT NOT NULL AUTO_INCREMENT,video VARCHAR(100) NOT NULL,type VARCHAR(10) NOT NULL,height INT NOT NULL,width INT NOT NULL,length VARCHAR(10) NOT NULL,PRIMARY KEY (video_id));")
    cursor.execute( "CREATE TABLE IF NOT EXISTS VideoObjects(object_id INT NOT NULL AUTO_INCREMENT,object_type VARCHAR(20) NOT NULL,`long` DOUBLE ,lat DOUBLE ,bb_x INT NOT NULL,bb_y INT NOT NULL,width INT NOT NULL,heigth INT NOT NULL,confidence  DOUBLE  NOT NULL,video_id INT NOT NULL,`timestamp` VARCHAR(30) NOT NULL,seconds VARCHAR(10) NOT NULL,PRIMARY KEY (object_id),FOREIGN KEY (video_id) REFERENCES Videos(video_id)ON DELETE CASCADE ON UPDATE CASCADE);")
                              
#Argumentos: Nombre del video (video) y el tipo (mp4)
#Return: El ID del video que hemos enviado
def getVideoID(video,type):
    connection=getMysqlConnection()
    cursor = connection.cursor(dictionary=True)
    sql='SELECT video_id FROM Videos where video=%s and type=%s'
    val = (video,type)
    cursor.execute(sql,val)
    result = cursor.fetchall()
    return result[0]['video_id']

#Guarda el video en la base de datos
#Argumentos: Un request del formulario
def saveVideoDB(f):
    try:
        path=Config.VIDEO_UPLOAD_FOLDER+f.filename
     
        video=cv2.VideoCapture(path)  # count the number of frames
        frames = video.get(cv2.CAP_PROP_FRAME_COUNT)
        fps = int(video.get(cv2.CAP_PROP_FPS))
        seconds = int(frames / fps) # calculate duration of the video


        name=fileName(f.filename)
        type=fileType(f.filename)
        width=video.get(cv2.CAP_PROP_FRAME_WIDTH)
        height=video.get(cv2.CAP_PROP_FRAME_HEIGHT)
        length = str(datetime.timedelta(seconds=seconds))

        connection=getMysqlConnection()
        cursor = connection.cursor(dictionary=True)
        sql='INSERT INTO Videos (video_id,video,type,height,width,length)VALUES (NULL,%s,%s,%s,%s,%s);'
        val = (name,type,height,width,length)
        cursor.execute(sql,val)
        connection.commit()
    except Exception as e:
        raise Exception('Error al guardar la imagen en la BD: ' + str(e)) from e

#Guarda los objetos en la base de datos
#Argumentos: Un JSON con las detecciones y el nombre entero de la imagen (imagen.png)
def saveVideoObjectsDB(detections,filename):
    try:
        connection=getMysqlConnection()
        i=0
        for img in detections:
            seconds="{0:5f}".format(img['segundos'])
            timestamp=img['Hora']
            length=len(img)
           
            for obj in range(length-2):
                long=0
                lat=0
                
                confidence=img['Objeto '+str(obj+1)]['Confianza']
                type=img['Objeto '+str(obj+1)]['Tipo']
               

                bb_x=img['Objeto '+str(obj+1)]['Bounding Box'][0]
                bb_y=img['Objeto '+str(obj+1)]['Bounding Box'][1]

                width=img['Objeto '+str(obj+1)]['Bounding Box'][2]
                heigth=img['Objeto '+str(obj+1)]['Bounding Box'][3] 

                video_id=getVideoID(fileName(filename),fileType(filename))
                cursor = connection.cursor(dictionary=True)
                sql='INSERT INTO VideoObjects (object_id,object_type,`long`,lat,bb_x,bb_y,width,heigth,confidence,video_id,timestamp,seconds)VALUES (NULL,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)';
                val = (type,long,lat,bb_x,bb_y,width,heigth,confidence,video_id,timestamp,seconds)
                cursor.execute(sql,val)
                connection.commit()

    except Exception as e:
        raise Exception('Error al guardar los objetos en la BD: ' + str(e)) from e

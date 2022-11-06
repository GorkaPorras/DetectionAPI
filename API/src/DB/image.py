import mysql.connector
from PIL import Image

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


#Crear DB de detecciones y tablas relacionadas con las imagenes si no existen
def createDB(connection):
    cursor = connection.cursor()
    cursor.execute("CREATE DATABASE IF NOT EXISTS Detections;")
    cursor.execute( "USE Detections;")

    cursor.execute("CREATE TABLE IF NOT EXISTS Images(image_id INT NOT NULL AUTO_INCREMENT,image VARCHAR(100) NOT NULL,type VARCHAR(10) NOT NULL,height INT NOT NULL,width INT NOT NULL,PRIMARY KEY (image_id));")
    cursor.execute( "CREATE TABLE IF NOT EXISTS Objects( object_id INT NOT NULL AUTO_INCREMENT,object_type VARCHAR(20) NOT NULL,`long` DOUBLE ,lat DOUBLE ,bb_x INT NOT NULL,bb_y INT NOT NULL,width INT NOT NULL,heigth INT NOT NULL,confidence  DOUBLE  NOT NULL,image_id INT NOT NULL,`timestamp` VARCHAR(30) NOT NULL,PRIMARY KEY (object_id),FOREIGN KEY (image_id) REFERENCES Images(image_id)ON DELETE CASCADE ON UPDATE CASCADE);")


#Argumentos: Nombre de la imagen (imagen) y el tipo (png)
#Return: El ID del la imagen que hemos enviado
def getImageID(image,type):
    connection=getMysqlConnection()
    cursor = connection.cursor(dictionary=True)
    sql='SELECT image_id FROM Images where image=%s and type=%s'
    val = (image,type)
    cursor.execute(sql,val)
    result = cursor.fetchall()
    return result[0]['image_id']

#Guarda la imagen en la base de datos
#Argumentos: Un request del formulario
def saveImagesDB(f):
    try:
        img=Image.open(f.stream)
        name=fileName(f.filename)
        type=fileType(f.filename)
        width=img.width
        height=img.height
        connection=getMysqlConnection()
        cursor = connection.cursor(dictionary=True)
        sql='INSERT INTO Images (image_id,image,type,height,width)VALUES (NULL,%s,%s,%s,%s);'
        val = (name,type,height,width)
        cursor.execute(sql,val)
        connection.commit()
    except Exception as e:
        raise Exception('Error al guardar la imagen en la BD: ' + str(e)) from e

#Guarda los objetos detectados en la imagen en la base de datos
#Argumentos: Un JSON con las detecciones y el nombre entero de la imagen (imagen.png)
def saveImageObjectsDB(detections,filename):
    try:
        connection=getMysqlConnection()
        timestamp=detections['Hora']
        length=len(detections)

        for obj in range(length-1):

            long=0
            lat=0

            confidence=detections['Objeto '+str(obj+1)]['Confianza']
            type=detections['Objeto '+str(obj+1)]['Tipo']

            bb_x=detections['Objeto '+str(obj+1)]['Bounding Box'][0]
            bb_y=detections['Objeto '+str(obj+1)]['Bounding Box'][1]

            width=detections['Objeto '+str(obj+1)]['Bounding Box'][2]
            heigth=detections['Objeto '+str(obj+1)]['Bounding Box'][3] 

            image_id=getImageID(fileName(filename),fileType(filename))
            
            cursor = connection.cursor(dictionary=True)
            sql='INSERT INTO Objects (object_id,object_type,`long`,lat,bb_x,bb_y,width,heigth,confidence,image_id,timestamp)VALUES (NULL,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)';
            val = (type,long,lat,bb_x,bb_y,width,heigth,confidence,image_id,timestamp)
            cursor.execute(sql,val)
            connection.commit()

    except Exception as e:
        raise Exception('Error al guardar los objetos en la BD: ' + str(e)) from e

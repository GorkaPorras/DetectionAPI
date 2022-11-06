import os
from flask import request
import cv2 
from random import randint

from config import Config

#Argumentos: El nombre entero de la imagen/video (imagen.png)
#Return: el tipo de la imagen/video (png)
def fileType(filename):
    return filename.rsplit('.', 1)[1]

#Argumentos: El nombre entero de la imagen/video (imagen.png)
#Return: El nombre de la imagen/video (imagen)
def fileName(filename):
    return filename.rsplit('.', 1)[0]

#Argumentos: El nombre entero de la imagen/video (imagen.png)
#Return Un boolean que nos indica si el imagen/video esta permitido
def isIMG(filename):
    ALLOWED_EXTENSIONS = set(['jpg', 'png','jpeg'])
    if fileType(filename) not in ALLOWED_EXTENSIONS:
            return False
    return True

#Subir la imagen a la carpeta static
#Argumentos: El archivo request del formulario
def uploadImage(f):
    try:
        name=fileName(f.filename)
        type=fileType(f.filename)
        filename = name+'.'+type
        f.save(os.path.join(Config.IMG_UPLOAD_FOLDER, filename))

    except Exception as e:
         raise Exception('No se ha guardado el archivo: ' + str(e)) from e

#Subir el video a la carpeta static
#Argumentos: El archivo request del formulario
def uploadVideo(f):
    try:
        name=fileName(f.filename)
        type=fileType(f.filename)
        filename = name+'.'+type
        f.save(os.path.join(Config.VIDEO_UPLOAD_FOLDER, filename))

    except Exception as e:
         raise Exception('No se ha guardado el archivo: ' + str(e)) from e

#Los argumentos son la imagen, las detecciones y un diccionario que relaciona colores y clases,
#que la primera vez se le pasará vacío a la funcion y este lo rellenará. Esto se hace así para,
#si se evalúa un video, mantener la misma relación entre color y clase.
def dibujar_detecciones(img,detecciones,clases={}):
  
    for i in detecciones.keys():
        if i[0]=='O':
            bbox=detecciones[i]["Bounding Box"]
            corner_lt=(bbox[0]-bbox[2]//2,bbox[1]-bbox[3]//2) #Left top
            corner_rb=(bbox[0]+bbox[2]//2,bbox[1]+bbox[3]//2) #Right bottom
            clase=detecciones[i]["Tipo"]
            confianza=detecciones[i]["Confianza"]
            if clase in clases.keys():
                color=clases[clase]
            else:
                clases[clase]=(randint(0,255),randint(0,255),randint(0,255)) #Color aleatorio
                color=clases[clase]
            
            cv2.rectangle(img,corner_lt,corner_rb,color,3)

            font = cv2.FONT_HERSHEY_SIMPLEX
            text="{}, {}%".format(clase,confianza)
            cv2.putText(img,text,corner_lt, font, 0.5,(0,0,0),1,cv2.LINE_AA,bottomLeftOrigin=False)
    
    cv2.imwrite('.'+Config.DETECTIONS_IMAGE,img)
    return img,clases
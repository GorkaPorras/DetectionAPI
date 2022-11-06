import os
from flask import jsonify,request,render_template
from werkzeug.utils import secure_filename
import socket
from struct import pack
import json
import cv2
from random import randint
from functions import dibujar_detecciones

from config import Config

#Argumentos:El nombre completo de la imagen (imagen.png)
#Return: Un JSON con las detecciones
def getImageJSON(filename):

    path=Config.IMG_UPLOAD_FOLDER+filename

    #Direccion IP 
    HOST = Config.DNN_ADDRESS_IMG   
    PORT = Config.DNN_PORT_IMG
   
    #Abrimos el archivo a enviar, en binario
    with open(path,'rb')as file:
        image=file.read()
    try:
        #Creamos el socket cliente
        with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
            s.connect((HOST, PORT))
            length = pack('>Q', len(image))  #Primero se envía el tamaño de la imagen
            s.sendall(length)
            s.sendall(image)
            recibido=s.recv(8192) #Si no llegan todas las detecciones, aumentar este número
            data=recibido.decode("utf-8") #Decodificamos el JSON
    except:
        data="Error al conectar con el socket images"

    return json.loads(data)  #Enviar el JSON 

#Argumentos:El nombre completo de la imagen (imagen.png)
#Crear un video con las detecciones
def createImageDetections(filename,detecciones):

    path=Config.IMG_UPLOAD_FOLDER+filename
    frame=cv2.imread('./'+path)
    
    dibujar_detecciones(frame,detecciones)#Dibujar las detecciones en la imagen/video




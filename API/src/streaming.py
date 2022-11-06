import socket
from struct import pack
import json
import io
import time
import cv2 as cv
import numpy as np

from functions import dibujar_detecciones
from config import Config

# Devuelve frame de la cámara con las detecciones 
def streamingVideo():
    darknet_width=640
    darknet_height=640

    #Dirección IP local de la Jetson
    HOST = Config.DNN_ADDRESS_VIDEO   
    PORT = Config.DNN_PORT_VIDEO

    URL = Config.VIDEOCAM_URL #Dirección IP de la camara
    cap=cv.VideoCapture(URL)

    if not cap.isOpened():
            print("Cannot open camera")
            exit()

    width = cap.get(cv.CAP_PROP_FRAME_WIDTH)
    height = cap.get(cv.CAP_PROP_FRAME_HEIGHT)

    if width>=640 and height>=640:
        resize=True
    else:
        resize=False

    #Diccionario que rellenará y usará la función dibujar_detecciones
    clases={}

    t1=time.time()
    fps=0
    n=1
    #Creamos el socket cliente
    with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
        s.connect((HOST, PORT))
        while True:
            ret,frame=cap.read()

            if ret:
                #Si la imagen es mas grande que lo que coge darknet, la resizeamos para enviar menos datos
                if resize:
                    frame_resize=cv.resize(frame,(darknet_width,darknet_height))
                else:
                    frame_resize=frame

                t3=time.time()
                data=io.BytesIO()
                np.save(data,frame_resize)
                image=data.getvalue()

                t4=time.time()
                print(len(image))
                print("Tiempo escritura/lectura: {}".format((t4-t3)))
                length = pack('>Q', len(image)) #Primero se envía el tamaño de la imagen
                s.sendall(length)
                s.sendall(image)
                recibido=s.recv(8192) #Si no llegan todas las detecciones, aumentar este número
                data=recibido.decode("utf-8") #Decodificamos el JSON
                detecciones_frame=json.loads(data) #Volcamos el JSON en un diccionario
                if resize:
                    for i in detecciones_frame.keys():
                        if i[0]=='O':
                            detecciones_frame[i]["Bounding Box"][0]=int(detecciones_frame[i]["Bounding Box"][0]*width/darknet_width)
                            detecciones_frame[i]["Bounding Box"][1]=int(detecciones_frame[i]["Bounding Box"][1]*height/darknet_height)
                            detecciones_frame[i]["Bounding Box"][2]=int(detecciones_frame[i]["Bounding Box"][2]*width/darknet_width)
                            detecciones_frame[i]["Bounding Box"][3]=int(detecciones_frame[i]["Bounding Box"][3]*height/darknet_height)

                imagen_render,clases=dibujar_detecciones(frame,detecciones_frame,clases)

                t2=time.time()
                t=t2-t1
                fps+=1/t
                fps_med=fps/n
                n+=1
                print("FPS medio: {}".format(fps_med))
                print(t2-t1)
                if n>20:
                    n=1
                    fps=0
                t1=time.time()

                (flag, encodedImage) = cv.imencode(".jpg", imagen_render)
                if not flag:
                    continue
                yield(b'--frame\r\n' b'Content-Type: image/jpeg\r\n\r\n' + bytearray(encodedImage) + b'\r\n')
            
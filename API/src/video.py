import socket
from struct import pack
import json
import cv2 
import numpy as np
import io

from functions import dibujar_detecciones
from config import Config

#Argumentos:El nombre completo del video (video.mp4)
#Return: Un JSON con las detecciones
def getVideoJSON(filename):
    path=Config.VIDEO_UPLOAD_FOLDER+filename
    
    #Direccion IP local de la Jetson
    HOST = Config.DNN_ADDRESS_VIDEO   
    PORT = Config.DNN_PORT_VIDEO


    vid=cv2.VideoCapture(path)
    totalNoFrames = int(vid.get(cv2.CAP_PROP_FRAME_COUNT))
    fps=vid.get(cv2.CAP_PROP_FPS)

    print("En el video hay {} frames y está a {} FPS".format(totalNoFrames,fps))

    width = vid.get(cv2.CAP_PROP_FRAME_WIDTH)
    height = vid.get(cv2.CAP_PROP_FRAME_HEIGHT)

    if width>=640 and height>=640:
        resize=True
    else:
        resize=False

    #Resolución que coge darknet
    darknet_width=640
    darknet_height=640

    detecciones=[]
    nframe=1

    #Creamos el socket cliente
    with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
        s.connect((HOST, PORT))
        while True:
            ret,frame=vid.read()
            #Cuando el video acabe, corta la conexión:
            if not ret:
                s.sendall(b'')
                break
            #Si la imagen es mas grande que lo que coge darknet, se encoge para enviar menos datos
            if resize:
                frame_resize=cv2.resize(frame,(darknet_width,darknet_height))
            else:
                frame_resize=frame

            data=io.BytesIO()
            np.save(data,frame_resize)
            image=data.getvalue()

            length = pack('>Q', len(image)) #Primero se envía el tamaño de la imagen
            s.sendall(length)
            s.sendall(image)
            
            recibido=s.recv(8192) #Si no llegan todas las detecciones, aumentar este número
            data=recibido.decode("utf-8") #Decodificamos el JSON
            detecciones_frame=json.loads(data) #Volcamos el JSON en un diccionario

            #Si se ha hecho un resize, hay que devolver las bbox al tamaño adecuado
            if resize:
                for i in detecciones_frame.keys():
                    if i[0]=='O':
                        detecciones_frame[i]["Bounding Box"][0]=int(detecciones_frame[i]["Bounding Box"][0]*width/darknet_width)
                        detecciones_frame[i]["Bounding Box"][1]=int(detecciones_frame[i]["Bounding Box"][1]*height/darknet_height)
                        detecciones_frame[i]["Bounding Box"][2]=int(detecciones_frame[i]["Bounding Box"][2]*width/darknet_width)
                        detecciones_frame[i]["Bounding Box"][3]=int(detecciones_frame[i]["Bounding Box"][3]*height/darknet_height)

            detecciones_frame['segundos']=nframe/fps
            nframe=nframe+1
            detecciones.append(detecciones_frame)
  
    vid.release()
    return detecciones


#Argumentos:El nombre completo del video (video.mp4)
#Crear un video con las detecciones
#Return: Un JSON con las detecciones
def createVideoDetections(filename):
    path1=Config.VIDEO_UPLOAD_FOLDER+filename

    #Direccion IP local de la Jetson
    HOST = Config.DNN_ADDRESS_VIDEO   
    PORT = Config.DNN_PORT_VIDEO

    vid=cv2.VideoCapture(path1)
    totalNoFrames = int(vid.get(cv2.CAP_PROP_FRAME_COUNT))
    fps=vid.get(cv2.CAP_PROP_FPS)
    print("En el video hay {} frames y está a {} FPS".format(totalNoFrames,fps))

    width = vid.get(cv2.CAP_PROP_FRAME_WIDTH)
    height = vid.get(cv2.CAP_PROP_FRAME_HEIGHT)

    if width>=640 and height>=640:
        resize=True
    else:
        resize=False

    #Resolución que coge darknet
    darknet_width=640
    darknet_height=640

    fourcc = cv2.VideoWriter_fourcc(*"vp80") #vp80 para webm
    video_out = cv2.VideoWriter('.'+Config.DETECTIONS_VIDEO, fourcc, fps, (int(width), int(height)))

    clases={}
    detecciones=[]
    nframe=1
    
    #Creamos el socket cliente
    with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
        s.connect((HOST, PORT))
        while True:
            ret,frame=vid.read()
            #Cuando el video acabe, corta la conexión:
            if not ret:
                s.sendall(b'')
                break
           #Si la imagen es mas grande que lo que coge darknet, se encoge para enviar menos datos
            if resize:
                frame_resize=cv2.resize(frame,(darknet_width,darknet_height))
            else:
                frame_resize=frame

            data=io.BytesIO()
            np.save(data,frame_resize)
            image=data.getvalue()

            length = pack('>Q', len(image)) #Primero se envía el tamaño de la imagen
            s.sendall(length)
            s.sendall(image)
            
            recibido=s.recv(8192) #Si no llegan todas las detecciones, aumentar este número
            data=recibido.decode("utf-8") #Decodificamos el JSON
            detecciones_frame=json.loads(data) #Volcamos el JSON en un diccionario

            #Si se ha hecho un resize, hay que devolver las bbox al tamaño adecuado
            if resize:
                for i in detecciones_frame.keys():
                    if i[0]=='O':
                        detecciones_frame[i]["Bounding Box"][0]=int(detecciones_frame[i]["Bounding Box"][0]*width/darknet_width)
                        detecciones_frame[i]["Bounding Box"][1]=int(detecciones_frame[i]["Bounding Box"][1]*height/darknet_height)
                        detecciones_frame[i]["Bounding Box"][2]=int(detecciones_frame[i]["Bounding Box"][2]*width/darknet_width)
                        detecciones_frame[i]["Bounding Box"][3]=int(detecciones_frame[i]["Bounding Box"][3]*height/darknet_height)

            imagen_render,clases=dibujar_detecciones(frame,detecciones_frame,clases)

            detecciones_frame['segundos']=nframe/fps
            nframe=nframe+1
            detecciones.append(detecciones_frame)
            print(str(nframe)+'/'+str(totalNoFrames)+' frames actualizados')
            
            video_out.write(imagen_render)
            
    video_out.release()
    cv2.destroyAllWindows()
    return detecciones

from flask import Flask, jsonify,request,render_template,Response
import time

from functions import *
from DB.image import *
from DB.video import *
from config import *
from streaming import streamingVideo
from video import createVideoDetections,getVideoJSON
from image import createImageDetections,getImageJSON


app=Flask(__name__)
app.config.from_object("config.Config")
app.config['SEND_FILE_MAX_AGE_DEFAULT'] = 0

#Página principal con una formulario para arrastrar imagenes y videos
@app.route('/')
def index():
    #cache.clear
    return render_template('upload_file.html')

#Esta dirección devuelve un JSON de detecciones o la imagen/video con las detenciones dibujadas.
@app.route("/api/detections", methods=['POST'])
def detections():
    
    try:
        f = request.files['image']
        f.filename=str(time.time())+"."+fileType(f.filename)
        
        if(isIMG(f.filename)):
            uploadImage(f)#Guardar imagen
            detecciones=getImageJSON(f.filename)
            saveImagesDB(f) #Guardar imagen en la base de datos
            saveImageObjectsDB(detecciones,f.filename) #Guardar objetos detectados en la base de datos
            if("json"==request.form['detections']): 
                return detecciones #Devolver JSON de detecciones
            else:
                createImageDetections(f.filename,detecciones) #Dibujar detecciones en la imagen
                return render_template("image.html", imagerute = '..'+Config.DETECTIONS_IMAGE) #Mostar imagen con las detecciones dibujadas
        else:
            uploadVideo(f) #Guardar video
            saveVideoDB(f)  #Guardar video DB

            if("json"==request.form['detections']):
                detecciones=getVideoJSON(f.filename) #Guardar objectos detectados en la BD
                saveVideoObjectsDB(detecciones,f.filename)
                return jsonify(detecciones) #Devolver JSON de detecciones
            else:
                detecciones=createVideoDetections(f.filename) #Dibujar detecciones en el video
                saveVideoObjectsDB(detecciones,f.filename) #Guardar objectos detectados en la BD
                return render_template("video.html", videorute = '..'+Config.DETECTIONS_VIDEO) #Mostar imagen con las detecciones dibujadas


    except Exception as e:
        return "Problem : " + str(e)
#Dirección de streaming
@app.route('/streaming')
def streaming():
    return render_template('streaming.html')
#streming
@app.route('/api/streaming')
def video_feed():
    return Response(streamingVideo(), mimetype='multipart/x-mixed-replace; boundary=frame')


#Algunas veces no muestra bien el video actual de detecciones. Con esta función se actualiza bien  el video actual
@app.after_request
def add_header(response):
    response.cache_control.no_store = True
    response.headers['Cache-Control'] = 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0'
    response.headers['Pragma'] = 'no-cache'
    response.headers['Expires'] = '-1'
    return response
  
if __name__ == '__main__':
    app.run(host='0.0.0.0')
    


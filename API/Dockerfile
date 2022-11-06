FROM python:3.8-slim

EXPOSE 5000

WORKDIR /app

COPY /src .
COPY /requirements.txt .

RUN apt-get upgrade
RUN pip install -r requirements.txt
RUN apt update && apt install -y libsm6 libxext6 ffmpeg libfontconfig1 libxrender1 libgl1-mesa-glx

ENTRYPOINT ["python"]
CMD ["app.py"]
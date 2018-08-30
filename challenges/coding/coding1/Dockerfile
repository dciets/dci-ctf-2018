FROM python:2.7-alpine

COPY . /app

RUN pip install -r /app/requirements.txt

WORKDIR /app

CMD ["python", "./app.py"]

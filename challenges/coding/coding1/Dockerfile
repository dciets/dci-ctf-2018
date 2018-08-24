FROM python:2.7-alpine

COPY . /app

RUN pip install -r /app/requirements.txt

EXPOSE 5001

CMD ["python", "/app/app.py"]

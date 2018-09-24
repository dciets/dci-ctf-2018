FROM python:3.6
RUN apt-get -y update
RUN pip3 install rsa flask
RUN mkdir /app
COPY AuthServer.py app.py SecureTransport.py Token.py flag password sign_key.pem /app/
WORKDIR /app
CMD ["flask", "run"]
FROM python:3.6
RUN pip3 install rsa
RUN mkdir /app
COPY AuthServer.py server.py SecureTransport.py Token.py flag password sign_key.pem /app/
WORKDIR /app
CMD ["python", "/app/server.py"]
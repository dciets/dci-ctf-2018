import asyncio
import base64
import datetime
import json
import rsa
import secrets
import struct

from hashlib import sha256
from SecureTransport import SecureTransport, xor
from Token import generate_token

from binascii import hexlify

class AuthServer(asyncio.Protocol):
    MAX_MESSAGE = 40

    def __init__(self, auth, key, flag):
        super().__init__()
        self.auth = auth
        self.key = rsa.PrivateKey.load_pkcs1(key)
        self.flag = flag

    def check_login(self, username, password):
        password = password.encode()
        attempt = sha256(password).hexdigest()
        real = self.auth[username] if username in self.auth else sha256(password).hexdigest()
        return attempt == real

    def connection_made(self, transport):
        key = secrets.token_bytes(AuthServer.MAX_MESSAGE)
        print("New host with key: %s" % hexlify(key).decode())

        host, port = transport.get_extra_info("peername")
        client_secret = int(host.split(".")[3])
        client_key = xor(key, bytes([client_secret]))

        transport.write(struct.pack("<B", len(client_key)))
        transport.write(client_key)
        transport.write(b"\n")

        self.transport = SecureTransport(transport, key)
        self.user = None

    def generate_host_token(self, host):
        if host != "admin.local":
            return None

        return generate_token(host, self.user, self.key)

    def data_received(self, data):
        data = self.transport.decrypt(data)

        if data is None:
            self.transport.write(b"Command too long\n")
            return

        lines = data.decode().strip().split("\n")

        if len(lines) == 0:
            self.transport.write(b"Invalid command\n")

        for line in lines:
            line = line.split(" ")
            command, args = line[0], line[1 :]

            if command == "LOGIN":
                if len(args) != 2:
                    self.transport.write(b"LOGIN requires 2 arguments\n")
                else:
                    username, password = args[0], args[1]

                    if self.check_login(username, password):
                        self.user = username
                        self.transport.write(b"Authentication successful\n")
                    else:
                        self.user = None
                        self.transport.write(b"Authentication failure\n")

            elif command == "FLAG":
                if len(args) != 0:
                    self.transport.write(b"FLAG requires 0 arguments\n")
                elif self.user != "alexander":
                    self.transport.write(b"Unauthorized command\n")
                else:
                    self.transport.write(b"%s\n" % self.flag)
            elif command == "TOKEN":
                if len(args) != 1:
                    self.transport.write(b"TOKEN requires 1 argument\n")
                elif self.user is None:
                    self.transport.write(b"Unauthorized command\n")
                else:
                    host = args[0]
                    token = self.generate_host_token(host)

                    if token:
                        self.transport.write(token)
                    else:
                        self.transport.write(b"Failed to generate token\n")
            else:
                self.transport.write(b"Invalid command\n")

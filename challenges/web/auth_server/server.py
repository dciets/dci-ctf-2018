#!/usr/bin/env python3
import asyncio
from AuthServer import AuthServer

auth = {}

with open("password", "r") as f:
    for line in f.readlines():
        line = line.strip()
        username, password = line.split(":")
        auth[username] = password

with open("sign_key.pem", "rb") as f:
    key = f.read()

with open("flag", "rb") as f:
    flag = f.read().strip()

loop = asyncio.get_event_loop()
server = loop.create_server(lambda: AuthServer(auth, key, flag), host="0.0.0.0", port=3000)
server = loop.run_until_complete(server)
loop.run_forever()

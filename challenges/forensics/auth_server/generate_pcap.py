#!/usr/bin/env python3
from progressbar import progressbar
import random
import rsa
import secrets
import struct

from scapy.all import wrpcap

from AuthServer import AuthServer
from Device import Device
from SecureTransport import SecureTransport, xor
from TCPStream import TCPStream
from TCPStreamTransport import TCPStreamTransport
from Token import generate_token

users = """yen
barney
dion
savanna
britt
angelina
ranae
argelia
yesenia
mark
jone
josue
sharri
johnny
cristi
scott
fausto
yasmine
scarlett
myesha
lona
ronald
katheleen
audrea
robert
eloy
ayana
molly
neal
edith"""

passwords = """DyUhEFCAKz9NBsMn
upJHwzmFyBR2NwDd
Kvb9JcWnPntUv5vW
cmN6GpNe7yajpWKE
3V3P9FDEQA35Zuzk
Mc7uZjHUyZFFE257
LN7MJzDqzXJzHJj2
Qhrx8RaxZr7uk68P
2fWMZp8L7p8P287t
prxuys3J8BKxhwAa
YkLR87KZxRgfSgL7
NSEppWcrgKkRWfNP
AFQpcJuUmnspBBrx
EQYB9Hzauup5ZtNE
cDgmhFxkNQAuWm3a
MRKyEddteDTesaz9
KVpR5hsvuSTSj2tq
dzheJgptdeHmP7Yb
qFJke4UZpmaSaLz5
kMv3h5pEzXAYykpD
LLGLRPLpREsbVD28
GGS8vM5VMxa4zArD
r5DuCDBbC7GUpuGm
ZJ77bbVQX8XjYpSz
kVGVkukgjufKKJ53
GhzRYvhh9de4vpyQ
ez89Ge2BtatHCjUL
AfZBdrGXqEBq7v6R
cgBudzMNUuJj9UZb
bEGyseZHm5L9YwAj"""

users = users.split("\n")
passwords = passwords.split("\n")

clients = []

for c in range(1, 5):
    for d in range(1, 255):
        clients.append((c, d))

random.shuffle(clients)

with open("sign_key.pem", "rb") as f:
    token_sign_key = rsa.PrivateKey.load_pkcs1(f.read())

server_device = Device("10.0.0.1")
key = secrets.token_bytes(AuthServer.MAX_MESSAGE)
packets = []

for client in progressbar(clients):
    address = "10.0.%d.%d" % (client[0], client[1])
    client_secret = client[1]
    client_device = Device(address)
    client_key = xor(key, bytes([client_secret]))

    if clients.index(client) == 784:
        user = "alexander"
        password = "UH3nSzXRd9MHrxp5"
    else:
        user = random.choice(users)
        password = passwords[users.index(user)]

    stream = TCPStream(client_device, server_device, 3000)
    stream.handshake()
    stream.server_send(struct.pack("<B", len(client_key)))
    stream.server_send(client_key)
    stream.server_send(b"\n")

    server = SecureTransport(TCPStreamTransport(stream.server_send), client_key)
    client = SecureTransport(TCPStreamTransport(stream.client_send), client_key)

    client.write(b"LOGIN %s %s" % (user.encode(), password.encode()))
    server.write(b"Authentication successful")
    client.write(b"TOKEN admin.local")
    server.write(generate_token("admin.local", user, token_sign_key))

    stream.fin()
    packets.extend(stream.packets)

wrpcap("capture.pcap", packets)

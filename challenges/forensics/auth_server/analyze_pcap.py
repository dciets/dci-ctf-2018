#!/usr/bin/env python3
from progressbar import progressbar
from scapy.all import *

from SecureTransport import xor

pcap = rdpcap("capture.pcap")
sessions = pcap.sessions()
session_keys = list(sessions.keys())

credentials = []

for i in progressbar(range(2, len(session_keys), 2)):
    client = sessions[session_keys[i]]
    client_secret = int(client[0][IP].src.split(".")[-1])

    server = sessions[session_keys[i + 1]]
    key_length = ord(server[1][TCP].load)
    key = server[2][TCP].load

    # print("Key length: %d" % key_length)
    # print("Client secret: %d" % client_secret)
    # print(b"Key: %s" % key)

    command = xor(client[5][TCP].load, key).decode()
    command = command.replace("LOGIN ", "")
    user, password = command.split(" ")
    if (user, password) not in credentials:
        credentials.append((user, password))

for login in credentials:
    print("%s:%s" % (login[0], login[1]))
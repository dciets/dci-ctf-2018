#!/usr/bin/env python2
from pwn import *
context.arch = "amd64"

code = """
    sub rsp, 8

    xor rax, rax
    xor rdi, rdi
    xor rdx, rdx
    mov rsi, rsp
    inc rdx
    syscall

    movzx rax, byte ptr [rsp]

    add rsp, 8
    ret
"""

final_code = """
    jmp msg
print:
    xor rax, rax
    inc rax
    pop rsi
    xor rdx, rdx
    add dl, 17
    syscall

    mov al, 60
    xor rdi, rdi
    syscall

msg:
    call print
"""

bytes = asm(code)
final_bytes = asm(final_code) + "Congratulations!\n"

def cstring(bytes):
    return "".join(["\\x%s" % (b.encode("hex")) for b in bytes])

def encrypt(assembly, key):
    result = ""
    for b in assembly:
        result += chr(ord(b) ^ ord(key) ^ 0xff)

    return result

with open("flag", "r") as f:
    flag = f.read()

start_key = "\x57"
data = [encrypt(bytes, start_key)]

for index in range(1, len(flag)):
    key = flag[index - 1]
    encrypted = encrypt(bytes, key)
    data += [encrypted]

data += [encrypt(final_bytes, flag[-1])]
print(len(data) - 1, len(data[0]), len(data[-1]), ord(start_key))

result = "\n".join('"%s"' % cstring(d) for d in data)
assert("\x00" not in result)
print(result)

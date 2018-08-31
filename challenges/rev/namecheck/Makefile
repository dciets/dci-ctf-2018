namecheckCC=gcc -no-pie

all: bin/namecheck

bin/namecheck: bin namecheck.c
	$(CC) namecheck.c -o bin/namecheck

bin:
	mkdir -p bin

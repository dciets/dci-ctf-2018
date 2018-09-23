main: main.c
	gcc -O0 -no-pie -o main main.c
	sed -i 's/\<hmmmmmmm\>/&?/' main

debug: main.c
	gcc -g -o debug main.c

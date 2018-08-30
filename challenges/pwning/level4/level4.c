#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

void ask_name() {
  char name[1024] = {0};
  printf("What is your name? ");
  read(0, name, 1024);
  printf("Hello, %s\n", name);
}

void jump() {
  char input[32] = {0};
  printf("Where do you want to jump? ");
  read(0, input, 31);
  unsigned long address = strtol(input, NULL, 16);
  unsigned long* stack = (unsigned long*)input;
  stack[7] = address;
}

int main(void) {
  setvbuf(stdout, NULL, _IONBF, 0);
  setvbuf(stdin, NULL, _IONBF, 0);

  ask_name();
  jump();

  return 0;
}
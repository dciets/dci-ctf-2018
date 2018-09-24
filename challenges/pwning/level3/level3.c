#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

int main(void) {
  char name[1024] = {0};

  setvbuf(stdout, NULL, _IONBF, 0);
  setvbuf(stdin, NULL, _IONBF, 0);
  printf("Buffer address: 0x%lx\n", (long unsigned int)name);
  puts("Can you hack me?");
  read(0, name, 1040);

  return 0;
}
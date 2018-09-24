#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

void debug_mode() {
  execve("/bin/bash", NULL, NULL);
}

int main(void) {
  char name[32] = {0};

  setvbuf(stdout, NULL, _IONBF, 0);
  setvbuf(stdin, NULL, _IONBF, 0);

  gets(name);
  printf("Welcome, %s", name);

  return 0;
}
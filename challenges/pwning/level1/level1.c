#include <stdio.h>
#include <stdlib.h>
#include <string.h>

int main(void) {
  char name[32] = {0};
  char current_mode[32] = "PRODUCTION";

  gets(name);
  printf("Welcome, %s", name);

  if(strcmp(current_mode, "DEBUG") == 0) {
    puts("Debug mode activated");
    system("/bin/bash");
  }
}
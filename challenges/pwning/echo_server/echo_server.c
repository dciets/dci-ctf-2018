#include <stdio.h>
#include <string.h>
#include <unistd.h>

void run_server() {
  char buffer[1024];

  while(1) {
    memset(buffer, 0, 1024);
    read(0, buffer, 1023);

    if(strcmp(buffer, "EXIT") == 0) {
      return;
    }

    printf(buffer);
  }
}

int main(void) {
  setvbuf(stdout, NULL, _IONBF, 0);
  setvbuf(stdin, NULL, _IONBF, 0);

  run_server();
  return 0;
}
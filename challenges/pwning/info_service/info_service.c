#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

char LAST_ERROR[256] = {0};

void save_error(const char* command) {
  snprintf(LAST_ERROR, 256, "Unknown command: %s", command);
}

int main(void) {
  char command[32] = {0};

  setvbuf(stdout, NULL, _IONBF, 0);
  setvbuf(stdin, NULL, _IONBF, 0);

  while(1) {
    printf("Command: ");
    gets(command);

    if(strcmp(command, "date") == 0) {
      system("date");
    } else if(strcmp(command, "processes") == 0) {
      system("echo $(ls /proc | grep -E '^[0-9]+$' | sort -n | wc -l) processes");
    } else if(strcmp(command, "os") == 0) {
      system("cat /etc/lsb-release  | tail -1 | cut -d '\"' -f2");
    } else if(strcmp(command, "clear") == 0) {
      system("clear");
    } else if(strcmp(command, "error") == 0) {
      printf("%s\n", LAST_ERROR);
    } else if(strcmp(command, "exit") != 0) {
      save_error(command);
      printf("%s\n", LAST_ERROR);
    } else {
      break;
    }
  }

  return 0;
}
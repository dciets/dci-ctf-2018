#include <signal.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>

#include <sys/mman.h>
#include <sys/ptrace.h>
#include <sys/reg.h>
#include <sys/user.h>
#include <sys/types.h>
#include <sys/wait.h>

#define MS * 1000
#define SEC MS * 1000

const char* DATA =
"\xe0\x2b\x44\xa0\xe0\x99\x68\xe0\x99\x57\xe0\x99\x7a\xe0\x21\x4e\xe0\x57\x6a\xa7\xad\xe0\xa7\x1e\xac\x8c\xe0\x2b\x6c\xa0\x6b"
"\xf3\x38\x57\xb3\xf3\x8a\x7b\xf3\x8a\x44\xf3\x8a\x69\xf3\x32\x5d\xf3\x44\x79\xb4\xbe\xf3\xb4\x0d\xbf\x9f\xf3\x38\x7f\xb3\x78"
"\xf4\x3f\x50\xb4\xf4\x8d\x7c\xf4\x8d\x43\xf4\x8d\x6e\xf4\x35\x5a\xf4\x43\x7e\xb3\xb9\xf4\xb3\x0a\xb8\x98\xf4\x3f\x78\xb4\x7f"
"\xfe\x35\x5a\xbe\xfe\x87\x76\xfe\x87\x49\xfe\x87\x64\xfe\x3f\x50\xfe\x49\x74\xb9\xb3\xfe\xb9\x00\xb2\x92\xfe\x35\x72\xbe\x75"
"\xcc\x07\x68\x8c\xcc\xb5\x44\xcc\xb5\x7b\xcc\xb5\x56\xcc\x0d\x62\xcc\x7b\x46\x8b\x81\xcc\x8b\x32\x80\xa0\xcc\x07\x40\x8c\x47"
"\x82\x49\x26\xc2\x82\xfb\x0a\x82\xfb\x35\x82\xfb\x18\x82\x43\x2c\x82\x35\x08\xc5\xcf\x82\xc5\x7c\xce\xee\x82\x49\x0e\xc2\x09"
"\xd3\x18\x77\x93\xd3\xaa\x5b\xd3\xaa\x64\xd3\xaa\x49\xd3\x12\x7d\xd3\x64\x59\x94\x9e\xd3\x94\x2d\x9f\xbf\xd3\x18\x5f\x93\x58"
"\xd6\x1d\x72\x96\xd6\xaf\x5e\xd6\xaf\x61\xd6\xaf\x4c\xd6\x17\x78\xd6\x61\x5c\x91\x9b\xd6\x91\x28\x9a\xba\xd6\x1d\x5a\x96\x5d"
"\x84\x4f\x20\xc4\x84\xfd\x0c\x84\xfd\x33\x84\xfd\x1e\x84\x45\x2a\x84\x33\x0e\xc3\xc9\x84\xc3\x7a\xc8\xe8\x84\x4f\x08\xc4\x0f"
"\x85\x4e\x21\xc5\x85\xfc\x0d\x85\xfc\x32\x85\xfc\x1f\x85\x44\x2b\x85\x32\x0f\xc2\xc8\x85\xc2\x7b\xc9\xe9\x85\x4e\x09\xc5\x0e"
"\x85\x4e\x21\xc5\x85\xfc\x0d\x85\xfc\x32\x85\xfc\x1f\x85\x44\x2b\x85\x32\x0f\xc2\xc8\x85\xc2\x7b\xc9\xe9\x85\x4e\x09\xc5\x0e"
"\x82\x49\x26\xc2\x82\xfb\x0a\x82\xfb\x35\x82\xfb\x18\x82\x43\x2c\x82\x35\x08\xc5\xcf\x82\xc5\x7c\xce\xee\x82\x49\x0e\xc2\x09"
"\x8f\x44\x2b\xcf\x8f\xf6\x07\x8f\xf6\x38\x8f\xf6\x15\x8f\x4e\x21\x8f\x38\x05\xc8\xc2\x8f\xc8\x71\xc3\xe3\x8f\x44\x03\xcf\x04"
"\xd3\x18\x77\x93\xd3\xaa\x5b\xd3\xaa\x64\xd3\xaa\x49\xd3\x12\x7d\xd3\x64\x59\x94\x9e\xd3\x94\x2d\x9f\xbf\xd3\x18\x5f\x93\x58"
"\xd5\x1e\x71\x95\xd5\xac\x5d\xd5\xac\x62\xd5\xac\x4f\xd5\x14\x7b\xd5\x62\x5f\x92\x98\xd5\x92\x2b\x99\xb9\xd5\x1e\x59\x95\x5e"
"\x82\x49\x26\xc2\x82\xfb\x0a\x82\xfb\x35\x82\xfb\x18\x82\x43\x2c\x82\x35\x08\xc5\xcf\x82\xc5\x7c\xce\xee\x82\x49\x0e\xc2\x09"
"\x82\x49\x26\xc2\x82\xfb\x0a\x82\xfb\x35\x82\xfb\x18\x82\x43\x2c\x82\x35\x08\xc5\xcf\x82\xc5\x7c\xce\xee\x82\x49\x0e\xc2\x09"
"\x8f\x44\x2b\xcf\x8f\xf6\x07\x8f\xf6\x38\x8f\xf6\x15\x8f\x4e\x21\x8f\x38\x05\xc8\xc2\x8f\xc8\x71\xc3\xe3\x8f\x44\x03\xcf\x04"
"\x8f\x44\x2b\xcf\x8f\xf6\x07\x8f\xf6\x38\x8f\xf6\x15\x8f\x4e\x21\x8f\x38\x05\xc8\xc2\x8f\xc8\x71\xc3\xe3\x8f\x44\x03\xcf\x04"
"\xd5\x1e\x71\x95\xd5\xac\x5d\xd5\xac\x62\xd5\xac\x4f\xd5\x14\x7b\xd5\x62\x5f\x92\x98\xd5\x92\x2b\x99\xb9\xd5\x1e\x59\x95\x5e"
"\x80\x4b\x24\xc0\x80\xf9\x08\x80\xf9\x37\x80\xf9\x1a\x80\x41\x2e\x80\x37\x0a\xc7\xcd\x80\xc7\x7e\xcc\xec\x80\x4b\x0c\xc0\x0b"
"\x83\x48\x27\xc3\x83\xfa\x0b\x83\xfa\x34\x83\xfa\x19\x83\x42\x2d\x83\x34\x09\xc4\xce\x83\xc4\x7d\xcf\xef\x83\x48\x0f\xc3\x08"
"\xd3\x18\x77\x93\xd3\xaa\x5b\xd3\xaa\x64\xd3\xaa\x49\xd3\x12\x7d\xd3\x64\x59\x94\x9e\xd3\x94\x2d\x9f\xbf\xd3\x18\x5f\x93\x58"
"\x8f\x44\x2b\xcf\x8f\xf6\x07\x8f\xf6\x38\x8f\xf6\x15\x8f\x4e\x21\x8f\x38\x05\xc8\xc2\x8f\xc8\x71\xc3\xe3\x8f\x44\x03\xcf\x04"
"\x80\x4b\x24\xc0\x80\xf9\x08\x80\xf9\x37\x80\xf9\x1a\x80\x41\x2e\x80\x37\x0a\xc7\xcd\x80\xc7\x7e\xcc\xec\x80\x4b\x0c\xc0\x0b"
"\x8e\x45\x2a\xce\x8e\xf7\x06\x8e\xf7\x39\x8e\xf7\x14\x8e\x4f\x20\x8e\x39\x04\xc9\xc3\x8e\xc9\x70\xc2\xe2\x8e\x45\x02\xce\x05"
"\x86\x4d\x22\xc6\x86\xff\x0e\x86\xff\x31\x86\xff\x1c\x86\x47\x28\x86\x31\x0c\xc1\xcb\x86\xc1\x78\xca\xea\x86\x4d\x0a\xc6\x0d"
"\xd5\x1e\x71\x95\xd5\xac\x5d\xd5\xac\x62\xd5\xac\x4f\xd5\x14\x7b\xd5\x62\x5f\x92\x98\xd5\x92\x2b\x99\xb9\xd5\x1e\x59\x95\x5e"
"\x81\x4a\x25\xc1\x81\xf8\x09\x81\xf8\x36\x81\xf8\x1b\x81\x40\x2f\x81\x36\x0b\xc6\xcc\x81\xc6\x7f\xcd\xed\x81\x4a\x0d\xc1\x0a"
"\x86\x4d\x22\xc6\x86\xff\x0e\x86\xff\x31\x86\xff\x1c\x86\x47\x28\x86\x31\x0c\xc1\xcb\x86\xc1\x78\xca\xea\x86\x4d\x0a\xc6\x0d"
"\xd1\x1a\x75\x91\xd1\xa8\x59\xd1\xa8\x66\xd1\xa8\x4b\xd1\x10\x7f\xd1\x66\x5b\x96\x9c\xd1\x96\x2f\x9d\xbd\xd1\x1a\x5d\x91\x5a"
"\x8f\x44\x2b\xcf\x8f\xf6\x07\x8f\xf6\x38\x8f\xf6\x15\x8f\x4e\x21\x8f\x38\x05\xc8\xc2\x8f\xc8\x71\xc3\xe3\x8f\x44\x03\xcf\x04"
"\xd2\x19\x76\x92\xd2\xab\x5a\xd2\xab\x65\xd2\xab\x48\xd2\x13\x7c\xd2\x65\x58\x95\x9f\xd2\x95\x2c\x9e\xbe\xd2\x19\x5e\x92\x59"
"\xd1\x1a\x75\x91\xd1\xa8\x59\xd1\xa8\x66\xd1\xa8\x4b\xd1\x10\x7f\xd1\x66\x5b\x96\x9c\xd1\x96\x2f\x9d\xbd\xd1\x1a\x5d\x91\x5a"
"\x85\x4e\x21\xc5\x85\xfc\x0d\x85\xfc\x32\x85\xfc\x1f\x85\x44\x2b\x85\x32\x0f\xc2\xc8\x85\xc2\x7b\xc9\xe9\x85\x4e\x09\xc5\x0e"
"\x80\x4b\x24\xc0\x80\xf9\x08\x80\xf9\x37\x80\xf9\x1a\x80\x41\x2e\x80\x37\x0a\xc7\xcd\x80\xc7\x7e\xcc\xec\x80\x4b\x0c\xc0\x0b"
"\x86\x4d\x22\xc6\x86\xff\x0e\x86\xff\x31\x86\xff\x1c\x86\x47\x28\x86\x31\x0c\xc1\xcb\x86\xc1\x78\xca\xea\x86\x4d\x0a\xc6\x0d"
"\x69\x94\xca\xb3\x42\xca\x7d\x42\xdc\xca\xb3\x50\x02\x40\x93\x8d\x87\x32\xbe\xca\xb3\x7d\x8d\x87\x6a\x67\x7d\x7d\x7d\xc1\xed\xec\xe5\xf0\xe3\xf6\xf7\xee\xe3\xf6\xeb\xed\xec\xf1\xa3\x88";

#define UNIT_COUNT 37
#define UNIT_SIZE 31
#define FINAL_SIZE 46
#define FINAL_STEP (FINAL_SIZE - UNIT_SIZE)
#define START_KEY 87

int main(void);

int get_regs(struct user_regs_struct* regs, pid_t child) {
  if(ptrace(PTRACE_GETREGS, child, NULL, regs) != 0) {
    return 0;
  } else {
    return 1;
  }
}

__attribute__((constructor))
void forkins() {
  pid_t child = fork();

  if(child == 0) {
    ptrace(PTRACE_TRACEME, 0, NULL, NULL);
    raise(SIGSTOP);
    return;
  }

  struct user_regs_struct regs;
  waitpid(child, NULL, 0);

  for(;;) {
    ptrace(PTRACE_SINGLESTEP, child, NULL, NULL);
    waitpid(child, NULL, 0);

    if(!get_regs(&regs, child)) {
      goto aborted;
    }

    if(regs.rip == (unsigned long)main) {
      break;
    }
  }

  char data[sizeof(long)] = {0};
  char key = START_KEY;

  for(int unit_index = 0; unit_index <= UNIT_COUNT; unit_index++) {

    for(int data_index = 0; data_index < (UNIT_SIZE + (unit_index / UNIT_COUNT) * FINAL_STEP); data_index += sizeof(long)) {
      for(int word_index = 0; word_index < sizeof(long); word_index++) {
        data[word_index] = DATA[UNIT_SIZE * unit_index + data_index + word_index] ^ key ^ 0xff;
      }

      ptrace(PTRACE_POKEDATA, child, regs.rip + data_index, *(unsigned long long*)(data));
    }

    for(;;) {
      ptrace(PTRACE_SINGLESTEP, child, NULL, NULL);
      int status;
      waitpid(child, &status, 0);

      if(status != 1407) {
        goto aborted;
      }

      if(!get_regs(&regs, child)) {
        goto aborted;
      }

      long code = ptrace(PTRACE_PEEKDATA, child, regs.rip, NULL);
      if((code & 0xff) == 0xc3) {
        key = (char)(regs.rax & 0xff);
        regs.rip = (unsigned long)main;
        ptrace(PTRACE_SETREGS, child, NULL, &regs);
        break;
      }
    }
  }

  kill(child, SIGKILL);
  exit(0);

aborted:
  kill(child, SIGKILL);
  exit(0);
}

int main(void) {
  char name[1024] = {0};

  printf("What is your name? ");
  scanf("%1023s", name);
  printf("Welcome, %s!", name);

  return 0;
}

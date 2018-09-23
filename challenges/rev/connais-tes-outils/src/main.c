#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <sys/ptrace.h>

void show_flag()
{
    char str[24] = "qv|Neagtvpjt{a|jqpw`rH";
    for(int i = 0; i < strlen(str); i++)
    {
        char tmp = str[i] ^ 0xCA;
        str[i] = ~tmp;
    }
    printf("%s", str);
}

int main(int argc, char** argv)
{
    if(ptrace(PTRACE_TRACEME,0,1,0) == -1)
    {
        show_flag();
        exit(0);
    }
    return 0;
}

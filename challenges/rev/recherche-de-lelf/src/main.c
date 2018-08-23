#include <stdlib.h>
#include <stdio.h>
#include <string.h>

void show_flag()
{
    const char* hint = "je me demande pourquoi il est ecrit elf au debut du fichier...";
    char str[17] = "st~L`x`hbyhq{vpJ";
    for(int i = 0; i < strlen(str); i++)
    {
        char tmp = str[i] ^ 0xC8;
        str[i] = ~tmp;
    }
    printf("%s", str);
}

int main(int argc, char** argv)
{
    show_flag();
    return 0;
}

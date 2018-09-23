#include <stdlib.h>
#include <stdio.h>
#include <sys/ptrace.h>
#include <string.h>

int main(int argc, char** argv)
{
    char buffer[33] = {0};
    char buffer_mod[33] = {0};
    const char* key = "wW~Xl@|C`I`WeSiIaSeSaEv7";
    if(ptrace(PTRACE_TRACEME,0,1,0) == -1)
    {
        printf("hmmm...\n il doit avoir un moyen de debugger meme avec un ptrace...\n reflechissez-y...\n");
        exit(0);
    }
    else
    {
		printf("veuillez entrer votre mot de passe: ");
		fgets(buffer, 32, stdin);

		int size = strlen(buffer);
		if(size = 0)
		{
			buffer[0] = 0;
		}
		else
		{
			buffer[size-1] = 0;
		}

		for(unsigned int i = 0; i < strlen(buffer)-1; i+=2)
		{
		    buffer_mod[i] = buffer[i] ^ 0x33;
		    buffer_mod[i+1] = buffer[i+1] ^ 0x16;
		}

		if(!strcmp(buffer_mod, key))
		{
		    printf("bravo, voici votre flag! DCI{%s}\n", buffer);
		}
		else
		{
		    printf("desoler... flag introuvable...\n");
		}
		return 0;
	}
}

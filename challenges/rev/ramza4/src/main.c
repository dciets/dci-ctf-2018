#include <stdlib.h>
#include <stdio.h>
#include <inttypes.h>
#include <string.h>
#include <assert.h>
#include <time.h>

#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>

#include <unistd.h>

#define WHITESPACE 64
#define EQUALS     65
#define INVALID    66

/* template for the decode key
const unsigned char diff_decode[] =
{
    66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
    66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
    66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
    66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
    66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
    66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
    66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
    66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
    66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
    66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
    66,66,66,66,66,66
};
*/

//base64 encode code found on https://en.wikibooks.org/wiki/Algorithm_Implementation/Miscellaneous/Base64
int hm(const void* data_buf, size_t dataLength, char* result, size_t resultSize, const char* base64chars)
{
   const uint8_t *data = (const uint8_t *)data_buf;
   size_t resultIndex = 0;
   size_t x;
   uint32_t n = 0;
   int padCount = dataLength % 3;
   uint8_t n0, n1, n2, n3;

   /* increment over the length of the string, three characters at a time */
   for (x = 0; x < dataLength; x += 3)
   {
      /* these three 8-bit (ASCII) characters become one 24-bit number */
      n = ((uint32_t)data[x]) << 16; //parenthesis needed, compiler depending on flags can do the shifting before conversion to uint32_t, resulting to 0

      if((x+1) < dataLength)
         n += ((uint32_t)data[x+1]) << 8;//parenthesis needed, compiler depending on flags can do the shifting before conversion to uint32_t, resulting to 0

      if((x+2) < dataLength)
         n += data[x+2];

      /* this 24-bit number gets separated into four 6-bit numbers */
      n0 = (uint8_t)(n >> 18) & 63;
      n1 = (uint8_t)(n >> 12) & 63;
      n2 = (uint8_t)(n >> 6) & 63;
      n3 = (uint8_t)n & 63;

      /*
       * if we have one byte available, then its encoding is spread
       * out over two characters
       */
      if(resultIndex >= resultSize) return 1;   /* indicate failure: buffer too small */
      result[resultIndex++] = base64chars[n0];
      if(resultIndex >= resultSize) return 1;   /* indicate failure: buffer too small */
      result[resultIndex++] = base64chars[n1];

      /*
       * if we have only two bytes available, then their encoding is
       * spread out over three chars
       */
      if((x+1) < dataLength)
      {
         if(resultIndex >= resultSize) return 1;   /* indicate failure: buffer too small */
         result[resultIndex++] = base64chars[n2];
      }

      /*
       * if we have all three bytes available, then their encoding is spread
       * out over four characters
       */
      if((x+2) < dataLength)
      {
         if(resultIndex >= resultSize) return 1;   /* indicate failure: buffer too small */
         result[resultIndex++] = base64chars[n3];
      }
   }

   /*
    * create and add padding that is required if we did not have a multiple of 3
    * number of characters available
    */
   if (padCount > 0)
   {
      for (; padCount < 3; padCount++)
      {
         if(resultIndex >= resultSize) return 1;   /* indicate failure: buffer too small */
         result[resultIndex++] = '=';
      }
   }
   if(resultIndex >= resultSize) return 1;   /* indicate failure: buffer too small */
   result[resultIndex] = 0;
   return 0;   /* indicate success */
}

uint8_t hmm(uint8_t var)
{
    return var ^ 0x36;
}

//base64 decode code found on https://en.wikibooks.org/wiki/Algorithm_Implementation/Miscellaneous/Base64
int hmmm (char *in, size_t inLen, unsigned char *out, size_t *outLen, const unsigned char* d)
{
    char *end = in + inLen;
    char iter = 0;
    uint32_t buf = 0;
    size_t len = 0;

    while (in < end) {
        unsigned char c = d[*in++];

        switch (c) {
        case WHITESPACE: continue;   /* skip whitespace */
        case INVALID:    return 1;   /* invalid input, return error */
        case EQUALS:                 /* pad character, end of data */
            in = end;
            continue;
        default:
            buf = buf << 6 | c;
            iter++; // increment the number of iteration
            /* If the buffer is full, split it into bytes */
            if (iter == 4) {
                if ((len += 3) > *outLen) return 1; /* buffer overflow */
                *(out++) = (buf >> 16) & 255;
                *(out++) = (buf >> 8) & 255;
                *(out++) = buf & 255;
                buf = 0; iter = 0;

            }
        }
    }

    if (iter == 3) {
        if ((len += 2) > *outLen) return 1; /* buffer overflow */
        *(out++) = (buf >> 10) & 255;
        *(out++) = (buf >> 2) & 255;
    }
    else if (iter == 2) {
        if (++len > *outLen) return 1; /* buffer overflow */
        *(out++) = (buf >> 4) & 255;
    }

    *outLen = len; /* modify to reflect the actual output size */
    return 0;
}

uint8_t hmmmm(uint8_t var)
{
    return var ^ 0x48;
}

uint8_t hmmmmm(uint8_t var)
{
    return var*2;
}

void hmmmmmm(const char* str)
{
    int fd = 0;
    if((fd = open("fichier.flag", O_CREAT | O_WRONLY, S_IRWXU)) == -1)
    {
        printf("erreur lors de la creation du flag...\n");
        exit(128);
    }
    else
    {
        write(fd, str, strlen(str));
    }
    close(fd);

    printf("voila! vous pouvez retrouvez votre flag dans vos fichier\n");
}

uint8_t hmmmmmmm(uint8_t var)
{
    return var/2;
}

uint8_t hmmmmmmmm(uint8_t var)
{
    uint8_t var1 = (rand()%1);
    return var1 + var;
}

//generate a decode key
void hmmmmmmmmm(unsigned char* key_decode, const char* key_encode)
{
    unsigned char key_temp[] =
    {
        66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
        66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
        66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
        66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
        66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
        66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
        66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
        66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
        66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
        66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,66,
        66,66,66,66,66,66
    };

    for(unsigned int i = 0; i < strlen(key_encode); i++)
    {
        *(key_temp + (unsigned char)*(key_encode+i)) = i;
    }
    *(key_temp + '=') = EQUALS;
    *(key_temp + '\n') = WHITESPACE;
    memcpy(key_decode, key_temp, 256);
}

void hmmmmmmmmmm(void* dest, void* src, size_t n)
{
    memcpy(dest, src, n);
}

//xor
uint8_t hmmmmmmmmmmm(uint8_t var1, uint8_t var2)
{
    return var1 ^ var2;
}

int main(int argc, char** argv)
{
    srand(time(NULL));
    char buf[1048] = {0};
    char answer[2048] = {0};

    const char* normal_base64= "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    const char* diff1_base64 = "SPQVWTUZ[XY^_\\]BC@AFGDEJKHspqvwtuz{xy~1|}bc`afgdejkh\"# !&\'$%*+9l"; //normal base64 xored by 0x12, replace 0x7f by 0x31 and replace '=' by l
    const char* diff2_base64 = "z0123456789+/ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxy";
    const char* diff3_base64 = "ABCdEfGHIJKLMNoPQRSTUVWXyZabDceFghijklmnOpqrstuvwxYz0123456789+/";

    //ascii art with epic font
    printf(" _______           _______  _______  _______    ______   _______ _________       \x0a(  ____ \\|\\     /|(  ____ )(  ____ \\(  ____ )  (  __  \\ (  ____ \\\\__   __/       \x0a| (    \\/| )   ( || (    )|| (    \\/| (    )|  | (  \\  )| (    \\/   ) (          \x0a| (_____ | |   | || (____)|| (__    | (____)|  | |   ) || |         | |          \x0a(_____  )| |   | ||  _____)|  __)   |     __)  | |   | || |         | |          \x0a      ) || |   | || (      | (      | (\\ (     | |   ) || |         | |          \x0a/\\____) || (___) || )      | (____/\\| ) \\ \\__  | (__/  )| (____/\\___) (___       \x0a\\_______)(_______)|/       (_______/|/   \\__/  (______/ (_______/\\_______/       \x0a                                                                                 \x0a _______           _______  _        _        _______  _        _______  _______ \x0a(  ____ \\|\\     /|(  ___  )( \\      ( \\      (  ____ \\( (    /|(  ____ \\(  ____ \\\x0a| (    \\/| )   ( || (   ) || (      | (      | (    \\/|  \\  ( || (    \\/| (    \\/\x0a| |      | (___) || (___) || |      | |      | (__    |   \\ | || |      | (__    \x0a| |      |  ___  ||  ___  || |      | |      |  __)   | (\\ \\) || | ____ |  __)   \x0a| |      | (   ) || (   ) || |      | |      | (      | | \\   || | \\_  )| (      \x0a| (____/\\| )   ( || )   ( || (____/\\| (____/\\| (____/\\| )  \\  || (___) || (____/\\\x0a(_______/|/     \\||/     \\|(_______/(_______/(_______/|/    )_)(_______)(_______/\x0a                                                                                 \x0a _______  _______  _______          _________ _        _______                   \x0a(       )(  ___  )(  ____ \\|\\     /|\\__   __/( (    /|(  ____ \\                  \x0a| () () || (   ) || (    \\/| )   ( |   ) (   |  \\  ( || (    \\/                  \x0a| || || || (___) || |      | (___) |   | |   |   \\ | || (__                      \x0a| |(_)| ||  ___  || |      |  ___  |   | |   | (\\ \\) ||  __)                     \x0a| |   | || (   ) || |      | (   ) |   | |   | | \\   || (                        \x0a| )   ( || )   ( || (____/\\| )   ( |___) (___| )  \\  || (____/\\                  \x0a|/     \\||/     \\|(_______/|/     \\|\\_______/|/    )_)(_______/                  \x0a");

    printf("\nceci est une super machine qui vous permet de cacher vos flag avant la sorti du challenge!!!\n");
    printf("entrez le flag a encrypter ici: ");
    fgets(buf, 1024 , stdin);
    int buf_len = strlen(buf);
    buf[buf_len] = 0;
    buf_len -= 1;
    printf("%d\n", buf_len);

    for(int i = 0; buf_len > 0; i++)
    {
        if(buf_len > 0)
        {
            char flag[9] = {0};
            char buf_split[7] = {0};
            hmmmmmmmmmm(buf_split, &buf[39*i], 6);
            hm(buf_split, strlen(buf_split), flag, 8, diff1_base64);
            hmmmmmmmmmm(&answer[52*i],flag,8);
            buf_len -= 6;
        }

        if(buf_len > 0)
        {
            char flag[9] = {0};
            unsigned char buf_split_1[7] = {0};
            unsigned char buf_split_2[7] = {0};
            hmmmmmmmmmm(buf_split_1, &buf[39*i], 6);
            hmmmmmmmmmm(buf_split_2, &buf[6+(39*i)], 6);
            for(int i = 0; i < 6; i++)
            {
                unsigned char temp = buf_split_1[i] ^ buf_split_2[i];
                buf_split_2[i] = temp;
            }
            hm(buf_split_2, 6, flag, 8, normal_base64);
            hmmmmmmmmmm(&answer[8+(52*i)], flag, 8);
            buf_len -= 6;
        }

        if(buf_len > 0)
        {
            char flag[5] = {0};
            unsigned char buf_split[4] = {0};

            hmmmmmmmmmm(buf_split, &buf[12+(39*i)], 3);
            for(int i = 0; i < 3; i++)
            {
                unsigned char a = hmmmm(buf_split[i]);
                buf_split[i] = a;
            }
            hm(buf_split, 3, flag, 4, diff3_base64);
            hmmmmmmmmmm(&answer[16+(52*i)], flag, 4);
            buf_len -= 3;
        }

        if(buf_len > 0)
        {
            char flag[9] = {0};
            unsigned char buf_split_1[7] = {0};
            unsigned char buf_split_2[7] = {0};
            hmmmmmmmmmm(buf_split_1, &buf[15+(39*i)], 6);
            hmmmmmmmmmm(buf_split_2, &buf[21+(39*i)], 6);
            for(int i = 0; i < 6; i++)
            {
                unsigned char temp1 = hmm(buf_split_1[i]);
                unsigned char temp2 = hmmmmmmmmmmm(buf_split_2[i], hmmmmmmmm(0x89));

                buf_split_2[i] = temp1 ^ temp2;
            }
            hm(buf_split_2,6,flag,8,diff1_base64);
            hmmmmmmmmmm(&answer[20+(52*i)], flag, 8);
            buf_len -= 6;
        }

        if(buf_len > 0)
        {
            char flag[9] = {0};
            unsigned char buf_split[7] = {0};

            hmmmmmmmmmm(buf_split, &buf[21+(39*i)], 6);
            for(int i = 0; i < 6; i++)
            {
                unsigned char a = hmmmmm(buf_split[i]);
                buf_split[i] = a;
            }
            hm(buf_split, 6, flag, 8, diff2_base64);
            hmmmmmmmmmm(&answer[28+(52*i)], flag, 8);
            buf_len -= 6;
        }

        if(buf_len > 0)
        {
            char flag[9] = {0};
            unsigned char buf_split_1[7] = {0};
            unsigned char buf_split_2[7] = {0};
            hmmmmmmmmmm(buf_split_1, &buf[27+(39*i)], 6);
            hmmmmmmmmmm(buf_split_2, &buf[33+(39*i)], 6);
            for(int i = 0; i < 6; i++)
            {
                unsigned char temp1 = hmmmmmmmmmmm(buf_split_1[i], hmmmmmmmm(0x02));
                unsigned char temp2 = hmmmmm(buf_split_2[i]);

                buf_split_2[i] = temp1 ^  hmmmmmmmmmmm(temp2, hmmmmmmmm(0x12));
            }
            hm(buf_split_2,6,flag,8,diff3_base64);
            hmmmmmmmmmm(&answer[36+(52*i)], flag, 8);
            buf_len -= 6;
        }

        if(buf_len > 0)
        {
            char flag[9] = {0};
            unsigned char buf_split[7] = {0};

            hmmmmmmmmmm(buf_split, &buf[33+(39*i)], 6);
            hm(buf_split, 6, flag, 8, normal_base64);
            hmmmmmmmmmm(&answer[44+(52*i)], flag, 8);
            buf_len -= 6;
        }
    }
    hmmmmmm(answer);
    return 0;
}

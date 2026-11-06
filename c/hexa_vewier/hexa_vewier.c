#include <stdio.h>

int main(int argc, char *argv[])
{
    if (argc != 2)
    {
        printf("Usage: %s <filename>\n", argv[0]);
        return 1;
    }

    FILE *file = fopen(argv[1], "rb");
    if (file == NULL)
    {
        printf("Error: cannot open file\n");
        return 1;
    }

    unsigned char buffer[16];
    int byte_read = 0;
    int offset = 0;

    printf("Offset    Hex Bytes                                        ASCII\n");
    printf("--------  -----------------------------------------------  ----------------\n");

    while ((byte_read = fread(buffer, 1, sizeof(buffer), file)) > 0)
    {
        printf("%08X  ", offset);

        for (int i = 0; i < byte_read; i++)
            printf("%02X ", buffer[i]);

        for (int i = byte_read; i < 16; i++)
            printf("   ");

        printf(" | ");

        for (int i = 0; i < byte_read; i++)
        {
            if (buffer[i] >= 32 && buffer[i] <= 126)
                printf("%c", buffer[i]);
            else
                printf(".");
        }

        printf("\n");
        offset += byte_read;
    }

    fclose(file);
    return 0;
}
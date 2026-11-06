#include <stdio.h>
#include <string.h>

#define MAX_LINE 512

int main(int argc, char *argv[])
{
    char line[MAX_LINE];
    int line_num = 1;
    int alert_count = 0;

    if (argc != 2)
    {
        printf("Usage: %s <logfile>\n", argv[0]);
        return 1;
    }

    FILE *logs_file = fopen(argv[1], "r");
    if (logs_file == NULL)
    {
        printf("Error opening file\n");
        return 1;
    }

    printf("Alert lines:\n");

    while (fgets(line, sizeof(line), logs_file) != NULL)
    {
        int is_failed = (strstr(line, "FAILED") != NULL);
        int is_404 = (strstr(line, " 404 ") != NULL) || (strstr(line, "\" 404 ") != NULL);

        if (is_failed || is_404)
        {
            alert_count++;
            printf("LN. %d : %s", line_num, line);
        }

        line_num++;
    }

    fclose(logs_file);

    if (alert_count == 1)
        printf("\nFound: 1 Alert.\n");
    else
        printf("\nFound: %d Alerts.\n", alert_count);

    return 0;
}
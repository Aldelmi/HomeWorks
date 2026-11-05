#include <stdio.h>
#include <string.h>
#include <ctype.h>

int check_password(char password[])
{
    int ChLenth, ChUpper, ChLower, ChNumber, ChSymbol;
    int score = 0;

    ChLenth = 0;
    ChLower = 0;
    ChNumber = 0;
    ChSymbol = 0;
    ChUpper = 0;

    int len = (int)strlen(password);

    printf(" Password Length: %d\n", len);

    if (len >= 8) {
        ChLenth = 1;
    }

    for (int i = 0; i < len; i++) {
        char c = password[i];

        if (isupper((unsigned char)c)) ChUpper = 1;
        if (islower((unsigned char)c)) ChLower = 1;
        if (isdigit((unsigned char)c)) ChNumber = 1;
        if (ispunct((unsigned char)c)) ChSymbol = 1;
    }

    score = ChLenth + ChLower + ChNumber + ChSymbol + ChUpper;

    printf(" Score: %d/5\n", score);

    if (score == 5) {
        printf(" Your Password is Strong\n");
    }
    else if (score >= 3) {
        printf(" Your Password is Acceptable\n");
    }
    else {
        printf(" Your Password is Weak\n");
    }

    if (ChLenth != 1) printf(" Your Password is Short\n");
    if (ChLower != 1) printf(" Your password dont have Lower cases 'a-z'\n");
    if (ChUpper != 1) printf(" Your Password dont have Upper cases 'A-Z'\n");
    if (ChSymbol != 1) printf(" Your Password dont have Symbol '!@#$^&'\n");
    if (ChNumber != 1) printf(" Your Password dont have numbers '0-9'\n");

    return score;
}

int main(void)
{
    char password[100] = "";
    int score = 0;
    int valid = 0;

    printf("===== Password Cheker =====\n");

    do {
        printf(" Enter Your Password :  ");

        if (fgets(password, sizeof(password), stdin) == NULL) {
            printf(" Error reading input\n");
            break;
        }

        password[strcspn(password, "\n")] = '\0';

        score = check_password(password);

        if (score == 5) {
            valid = 1;
        }

    } while (valid != 1);

    return 0;
}

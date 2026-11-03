#include <stdio.h>
#include <string.h>
#include <ctype.h>

int check_password(const char *password) {
    int has_upper = 0;
    int has_lower = 0;
    int has_digit = 0;
    int has_symbol = 0;
    int length = strlen(password);

    if (length <= 8) {
        printf(" Password must be MORE than 8 characters.\n");
        return 0;
    }

    for (int i = 0; i < length; i++) {
        unsigned char c = password[i];
        if (isupper(c))       has_upper = 1;
        else if (islower(c))  has_lower = 1;
        else if (isdigit(c))  has_digit = 1;
        else if (ispunct(c))  has_symbol = 1;   
    }

    int ok = 1;

    if (!has_upper)  { printf(" Missing UPPERCASE letter.\n"); ok = 0; }
    if (!has_lower)  { printf(" Missing lowercase letter.\n"); ok = 0; }
    if (!has_digit)  { printf(" Missing number.\n");          ok = 0; }
    if (!has_symbol) { printf(" Missing symbol (e.g., !@#$).\n"); ok = 0; }

    return ok;
}

int main(void) {
    char password[128];

    printf("Enter your password: ");
    if (scanf("%127s", password) != 1) {
        printf("Input error.\n");
        return 1;
    }

    if (check_password(password)) {
        printf(" Password is STRONG.\n");
    } else {
        printf("  Password is WEAK.\n");
    }

    return 0;
}

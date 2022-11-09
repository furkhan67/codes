#include <stdio.h>

void main()
{
    int a;
    printf("Enter a number: ");
    scanf("%d",&a);
    if(a&1==1){printf("%d is ODD\n", a);}
    else {printf("%d is EVEN\n", a);}

}

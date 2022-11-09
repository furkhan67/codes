#include<stdio.h>
#include<conio.h>

void main()
{
int a[5];
int b;
int sum=0;
int c;


printf("Enter the elements");


for (b=0;b<5;b++)


{
    scanf("%d",&a[b]);

}

for (b=0;b<5;b++)


{
    printf("a[%d] is %d\n",b,a[b]);
}

for (b=0;b<=4;b++)
{

sum=sum+a[b];

}
printf("Sum is %d",sum);

getch();

}

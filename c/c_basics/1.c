#include<stdio.h>

void main()


{

int a,i,k;
a=0;
i=0;
printf("Enter 5 Nos.\t");
do{
        if(i<=5)
            {scanf("%d",&k);
              a=a+k;
              i++;}

} while(i<5);


printf("%d",a);
}




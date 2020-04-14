#include<stdio.h>
#include<conio.h>

struct blood_bank
{
char b_group[3];
float blood_id;
char donor[50];
char receiver[50];




}l;


void main()


{

printf("Enter the Blood Group\t");
scanf("%s",&l.b_group);

printf("Enter Donor Name\t");
scanf("%s",&l.donor);

printf("Enter Receiver Name\t");
scanf("%s",&l.receiver);

printf("Enter Blood ID\t");
scanf("%f",&l.blood_id);



printf("Blood Group  is %s\n",l.b_group);
printf("Blood ID is %f\n",l.blood_id);
printf("Donor Name is %s\n",l.donor);
printf("Receiver Name is %s",l.receiver);
}

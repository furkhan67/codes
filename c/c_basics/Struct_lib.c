#include<stdio.h>
#include<conio.h>

struct library
{

int book_id;
char book_n[50];
char author_n[50];
float book_p;



}l;


void main()


{

printf("Enter the Book ID\t");
scanf("%d",&l.book_id);

printf("Enter Book Name\t");
scanf("%s",&l.book_n);

printf("Enter Author Name\t");
scanf("%s",&l.author_n);

printf("Enter Book Price\t");
scanf("%f",&l.book_p);



printf("Book Name is %s\n",l.book_n);
printf("Book Price is %f\n",l.book_p);
printf("Author Name is %s\n",l.author_n);
printf("Book ID is %d",l.book_id);
}

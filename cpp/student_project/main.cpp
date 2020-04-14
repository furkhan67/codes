#include<iostream>
#include "student_info_structure.h"
#include "receive.h"
using namespace std;
//#include"find_index.h"
//#include"Student_info_modify.h"
//#include"Student_info_modify_show.h"
int Total_number_of_students=100;
using namespace std;
int main()
{
    char flag,ch,ch2;
    int i,in;
    double fns;
    stu Current_students_data[100];


    do
    {
        std::cout<<"Enter choice :";
        std::cin>>ch;
        if (ch=='a')
        {
            do
            {
                cout<<"Enter No. of students (Atmost 100 students): ";
                std::cin>>Total_number_of_students;
            }
            while(Total_number_of_students>100);
            for (i = 0; i < Total_number_of_students; i++)
            {
                Student_info_receive( Current_students_data, i );

            }
        }
        /*else if(ch=='b'){

                          std::cout<<"Enter Student's number: ";
                          std::cin>>fns;
                          in=Find_student_index(Current_students_data,fns,Total_number_of_students);
                          Student_info_modify_show(in,Current_students_data);
                          std::cout<<"Modify?(y/n)";
                          std::cin>>ch2;
                          if (ch2=='y'){
                            Student_info_modify(in,Current_students_data);
                          }

                        }

        */cout<<"Continue?(y/n)";
        cin>>flag;


    }
    while(flag=='y');
    return 0;









}

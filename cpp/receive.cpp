#include<iostream>
#include "student_info_structure.h"
void Student_info_receive(stu ref[],int a){

int i=a;
std::cout<<"Enter Surname :";
std::cin>>ref[i].sn;

std::cout<<"Enter firstname :";
std::cin>>ref[i].fn;

std::cout<<"Enter the student number(between 97000000-99999999) :";
std::cin>>ref[i].sn;
while (ref[i].sn<97000000 && ref[i].sn>99999999){std::cout<<"Enter the student number(between 97000000-99999999) :";}




}

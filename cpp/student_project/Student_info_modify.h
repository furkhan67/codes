#define SIM_H
#ifndef SIM_H
#include<iostream>
#include "student_info_structure.h"

void Student_info_modify(int b,stu ref[]){

int i=b;

  int i=a; \std::cout<<"Enter Surname :";
  std::cin>>ref[i].sn;
  //std::cout<<ref[i].sn;

  std::cout<<"\nEnter firstname :";
  std::cin>>ref[i].fn;

  std::cout<<"\nEnter the student number(between 97000000-99999999) :";
  std::cin>>ref[i].prn;
  int x=ref[i].prn;
  while ((ref[i].prn>0 && ref[i].prn<97000000) || ref[i].prn>99999999){
    std::cout<<"\nEnter the student number(between 97000000-99999999) :";
    std::cin>>ref[i].prn;
                                                    }

  std::cout<<"\nEnter Year of Entrance :";
  std::cin>>ref[i].ye;
  while (ref[i].ye<1300 || ref[i].ye>1499){
    std::cout<<"\nEnter the student's Year of Entrance(between1300-1499) :";
    std::cin>>ref[i].ye;}

    std::cout<<"\nEnter CGPA :";
    std::cin>>ref[i].gp;
    while (ref[i].gp<0 || ref[i].gp>20){
      std::cout<<"\nEnter the student's CGPA(between 0-20) :";
      std::cin>>ref[i].gp;}

      std::cout<<"\nEnter No. of Units passed until now :";
      std::cin>>ref[i].nup;
      while (ref[i].nup<0 || ref[i].nup>200){
        std::cout<<"\nEnter the No. of units passed until now(between 0-200) :";
        std::cin>>ref[i].nup;}
}
#endif

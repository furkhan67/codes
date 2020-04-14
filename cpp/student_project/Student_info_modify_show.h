#define SIMS_H
#ifndef SIMS_H
#include<iostream>
#include "student_info_structure.h"
void Student_info_modify_show(int b,stu ref[]){
  std::cout<<"Surname :"<<ref.sn<<endl;
  std::cout<<"Firstname :"<<ref.fn<<endl;
  std::cout<<"Student Number :"<<ref.prn<<endl;
  std::cout<<"Year of Entrance :"<<ref.ye<<endl;
  std::cout<<"CGPA :"<<ref.gp<<endl;
}



#endif

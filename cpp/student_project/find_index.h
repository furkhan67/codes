#ifndef FIND_INDEX_H
#define FIND_INDEX_H
#include<iostream>
#include "student_info_structure.h"
int Find_student_index(stu ref[],double a,int tnos){

  for (int i=0;i<tnos;i++){


    if (a==ref[i].prn){return i;}
  }



}
#endif

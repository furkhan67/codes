#include<iostream>
using namespace std;

class Complex
{public:
        int real,imag;
        Complex(int x=0,int y=0){real=x;imag=y;}
        void print(){cout << real << " + i"<< imag << '\n';}

      //  friend Complex operator + (Complex const &, Complex const &);
};
        Complex operator +(Complex obj1, Complex obj2)
        {

            return Complex(obj1.real + obj2.real, obj1.imag + obj2.imag);

          }









int main(){

Complex obj1(10,15),obj2(5,10);
Complex c3=obj1 + obj2;
c3.print();



}

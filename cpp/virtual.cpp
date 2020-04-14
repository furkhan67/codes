#include <iostream>
#include<string>

using namespace std;

class Base{
private:
public:
	virtual string getName() {
		return  "Base Class" ;
	}
};

class Child : public Base{
public:
	string getName() {
		return "Child class" ;
	}

};


int main() {
	Child Me;
	Base *I = &Me;

	cout << I->getName() << endl;



}

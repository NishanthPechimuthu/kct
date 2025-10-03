#include <iostream>
using namespace std;

class Base1 {
protected:
    float basic;
public:
    Base1(float b) {
        cout << "Base1 constructor called" << endl;
        basic = b;
    }
};

class Base2 {
protected:
    float bonusPercent;
public:
    Base2(float bp) {
        cout << "Base2 constructor called" << endl;
        bonusPercent = bp;
    }
};

class Employee : public Base1, public Base2 {
    float grossSalary;
public:
    Employee(float b, float bp) : Base1(b), Base2(bp) {
        cout << "Employee constructor called" << endl;
        grossSalary = basic + (basic * bonusPercent / 100);
    }

    void display() {
        cout << "Basic Salary: " << basic << endl;
        cout << "Bonus %: " << bonusPercent << endl;
        cout << "Gross Salary: " << grossSalary << endl;
    }
};

int main() {
    float basic, bonus;
    cout << "Enter basic salary: ";
    cin >> basic;
    cout << "Enter bonus percentage: ";
    cin >> bonus;

    Employee e(basic, bonus);
    cout << "\nSalary Details\n";
    e.display();

    return 0;
}

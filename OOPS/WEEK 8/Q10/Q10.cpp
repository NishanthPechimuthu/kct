#include <iostream>
using namespace std;

class Employee {
protected:
    int id;
    string name;
public:
    void setEmployee(int eid, string ename) {
        id = eid;
        name = ename;
    }
    void displayEmployee() {
        cout << "Employee ID: " << id << endl;
        cout << "Name: " << name << endl;
    }
};

class Salary {
protected:
    double basic, DA, HRA;
public:
    void setSalary(double b, double d, double h) {
        basic = b;
        DA = d;
        HRA = h;
    }
    double calculateBaseSalary() {
        return basic + DA + HRA;
    }
};

class Manager : public Employee, public Salary {
private:
    double allowance;
public:
    void setAllowance(double a) {
        allowance = a;
    }
    void displayGrossSalary() {
        displayEmployee();
        double gross = calculateBaseSalary() + allowance;
        cout << "Gross Salary: " << gross << endl;
    }
};

int main() {
    Manager m;
    int id;
    string name;
    double basic, DA, HRA, allowance;

    cout << "Enter Employee ID and Name: ";
    cin >> id >> name;
    cout << "Enter Basic Pay, DA, HRA: ";
    cin >> basic >> DA >> HRA;
    cout << "Enter Special Allowance: ";
    cin >> allowance;

    m.setEmployee(id, name);
    m.setSalary(basic, DA, HRA);
    m.setAllowance(allowance);

    m.displayGrossSalary();

    return 0;
}

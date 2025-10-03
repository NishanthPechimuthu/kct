#include <iostream>
using namespace std;

class Person {
protected:
    string name;
public:
    void setName(string n) {
        name = n;
    }
    void displayName() {
        cout << "Name: " << name << endl;
    }
};

class Test : virtual public Person {
protected:
    int academicMarks;
public:
    void setAcademicMarks(int m) {
        academicMarks = m;
    }
};

class Sports : virtual public Person {
protected:
    int sportsMarks;
public:
    void setSportsMarks(int m) {
        sportsMarks = m;
    }
};

class Result : public Test, public Sports {
public:
    void displayResult() {
        displayName();
        cout << "Academic Marks: " << academicMarks << endl;
        cout << "Sports Marks: " << sportsMarks << endl;
        cout << "Total Score: " << (academicMarks + sportsMarks) << endl;
    }
};

int main() {
    Result r;
    r.setName("blk");
    r.setAcademicMarks(75);
    r.setSportsMarks(15);
    r.displayResult();
    return 0;
}

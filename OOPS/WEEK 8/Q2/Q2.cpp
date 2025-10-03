#include <iostream>
using namespace std;

class Person {
protected:
    string name;
    int age;
public:
    void getPersonData() {
        cout << "Enter name: ";
        cin >> name;
        cout << "Enter age: ";
        cin >> age;
    }
    void displayPersonData() {
        cout << "Name: " << name << endl;
        cout << "Age: " << age << endl;
    }
};

class Student : public Person {
protected:
    int marks[3];
public:
    void getStudentData() {
        getPersonData();
        cout << "Enter marks for 3 subjects: " << endl;
        for(int i = 0; i < 3; i++) {
            cout << "Subject " << i+1 << ": ";
            cin >> marks[i];
        }
    }
    void displayStudentData() {
        displayPersonData();
        cout << "Marks: ";
        for(int i = 0; i < 3; i++) {
            cout << marks[i] << " ";
        }
        cout << endl;
    }
};

class GraduateStudent : public Student {
private:
    float average;
    float percentage;
public:
    void calculateResult() {
        int sum = 0;
        for(int i = 0; i < 3; i++) {
            sum += marks[i];
        }
        average = sum / 3.0;
        percentage = (sum / (3.0 * 100)) * 100;
    }
    void displayResult() {
        displayStudentData();
        cout << "Average Marks: " << average << endl;
        cout << "Percentage: " << percentage << "%" << endl;
    }
};

int main() {
    GraduateStudent gs;
    gs.getStudentData();
    gs.calculateResult();
    cout << "\nStudent Details & Result\n";
    gs.displayResult();
    return 0;
}

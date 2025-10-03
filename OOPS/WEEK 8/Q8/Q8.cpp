#include <iostream>
using namespace std;

class Base {
protected:
    int mark1, mark2, mark3;
public:
    void setMarks(int m1, int m2, int m3) {
        mark1 = m1;
        mark2 = m2;
        mark3 = m3;
    }
};

class Intermediate : public Base {
protected:
    int total;
public:
    void calculateTotal() {
        total = mark1 + mark2 + mark3;
    }
    int getTotal() {
        return total;
    }
};

class Derived : public Intermediate {
private:
    double average;
public:
    void calculateAverage() {
        average = total / 3.0;
    }
    void display() {
        cout << "Total: " << total << endl;
        cout << "Average: " << average << endl;
    }
};

int main() {
    Derived d;
    int m1, m2, m3;

    cout << "Enter 3 marks: ";
    cin >> m1 >> m2 >> m3;

    d.setMarks(m1, m2, m3);
    d.calculateTotal();
    d.calculateAverage();
    d.display();

    return 0;
}

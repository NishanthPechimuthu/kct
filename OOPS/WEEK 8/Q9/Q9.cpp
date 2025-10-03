#include <iostream>
using namespace std;

class A {
public:
    double x;
    void setCost(double c) {
        x = c;
    }
};

class B {
public:
    double x;
    void setSelling(double s) {
        x = s;
    }
};

class C : public A, public B {
public:
    double calculateProfit() {
        return B::x - A::x;
    }
};

int main() {
    C obj;
    double cost, selling;

    cout << "Enter cost price: ";
    cin >> cost;
    cout << "Enter selling price: ";
    cin >> selling;

    obj.setCost(cost);
    obj.setSelling(selling);

    double profit = obj.calculateProfit();
    if(profit > 0)
        cout << "Profit: " << profit << endl;
    else if(profit < 0)
        cout << "Loss: " << -profit << endl;
    else
        cout << "No Profit No Loss" << endl;

    return 0;
}

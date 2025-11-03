#include <iostream>
#include <cmath>
#include <iomanip>
using namespace std;

int power(int base, int exponent) {
    return pow(base, exponent);
}

double power(double base, int exponent) {
    return pow(base, exponent);
}

int main() {
    string type;
    cin >> type;

    cout << fixed << setprecision(2);

    if (type == "int") {
        int base, exp;
        cin >> base >> exp;
        cout << power(base, exp);
    }
    else if (type == "double") {
        double base;
        int exp;
        cin >> base >> exp;
        cout << power(base, exp);
    }
    else {
        cout << "Invalid type";
    }

    return 0;
}

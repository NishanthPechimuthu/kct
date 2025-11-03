#include <iostream>
#include <cmath>
#include <iomanip>
using namespace std;

double volume(double side) {
    return pow(side, 3);
}

double volumeSphere(double radius) {
    return (4.0 / 3.0) * M_PI * pow(radius, 3);
}

double volume(double radius, double height) {
    return M_PI * pow(radius, 2) * height;
}

int main() {
    string shape;
    cin >> shape;

    cout << fixed << setprecision(2);

    if (shape == "cube") {
        double side;
        cin >> side;
        cout << volume(side);
    }
    else if (shape == "sphere") {
        double radius;
        cin >> radius;
        cout << volumeSphere(radius);
    }
    else if (shape == "cylinder") {
        double radius, height;
        cin >> radius >> height;
        cout << volume(radius, height);
    }
    else {
        cout << "Invalid shape type";
    }

    return 0;
}

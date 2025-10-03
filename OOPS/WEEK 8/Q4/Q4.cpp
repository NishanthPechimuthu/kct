#include <iostream>
using namespace std;

class Rectangle {
public:
    void calculateArea(int length, int breadth) {
        cout << "Area of Rectangle: " << (length * breadth) << endl;
    }
};

class Triangle {
public:
    void calculateArea(double base, double height) {
        cout << "Area of Triangle: " << (0.5 * base * height) << endl;
    }
};

class ShapeCalculator : public Rectangle, public Triangle {
public:
    void rectangleArea(int length, int breadth) {
        Rectangle::calculateArea(length, breadth);
    }
    void triangleArea(double base, double height) {
        Triangle::calculateArea(base, height);
    }
};

int main() {
    ShapeCalculator sc;
    sc.rectangleArea(10, 5);
    sc.triangleArea(6.0, 4.0);
    return 0;
}

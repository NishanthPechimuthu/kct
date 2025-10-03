#include <iostream>
using namespace std;

class Flyable {
protected:
    double flyingSpeed, flyingTime;
public:
    void setFlying(double speed, double time) {
        flyingSpeed = speed;
        flyingTime = time;
    }
    double flyDistance() {
        return flyingSpeed * flyingTime;
    }
};

class Swimmable {
protected:
    double swimmingSpeed, swimmingTime;
public:
    void setSwimming(double speed, double time) {
        swimmingSpeed = speed;
        swimmingTime = time;
    }
    double swimDistance() {
        return swimmingSpeed * swimmingTime;
    }
};

class Duck : public Flyable, public Swimmable {
public:
    double totalDistance() {
        return flyDistance() + swimDistance();
    }
};

int main() {
    Duck d;
    double fSpeed, fTime, sSpeed, sTime;

    cout << "Enter flying speed and time: ";
    cin >> fSpeed >> fTime;
    cout << "Enter swimming speed and time: ";
    cin >> sSpeed >> sTime;

    d.setFlying(fSpeed, fTime);
    d.setSwimming(sSpeed, sTime);

    cout << "Total distance covered by Duck: " << d.totalDistance() << endl;

    return 0;
}

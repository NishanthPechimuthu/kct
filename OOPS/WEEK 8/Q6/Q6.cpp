#include <iostream>
using namespace std;

class Animal {
protected:
    double foodEnergy;
public:
    void setFoodEnergy(double cal) {
        foodEnergy = cal;
    }
};

class Mammal : public Animal {
protected:
    double activityEnergy;
public:
    void setActivityEnergy(double cal) {
        activityEnergy = cal;
    }
};

class Human : public Mammal {
private:
    double studyEnergy;
public:
    void setStudyEnergy(double cal) {
        studyEnergy = cal;
    }
    void calculateRemainingEnergy() {
        double remaining = foodEnergy - (activityEnergy + studyEnergy);
        cout << "Remaining Energy: " << remaining << " calories" << endl;
    }
};

int main() {
    Human h;
    h.setFoodEnergy(2000);
    h.setActivityEnergy(500);
    h.setStudyEnergy(300);
    h.calculateRemainingEnergy();
    return 0;
}

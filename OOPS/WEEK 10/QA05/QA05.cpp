#include <iostream>
#include <string>
using namespace std;

class Membership {
private:
    string name;
    int duration;
    string plan;

public:
    // Constructor 1: Name only
    Membership(string n) {
        name = n;
        duration = -1;
        plan = "NA";
    }

    // Constructor 2: Name + Duration
    Membership(string n, int d) {
        name = n;
        duration = d;
        plan = "NA";
    }

    // Constructor 3: Name + Duration + Plan
    Membership(string n, int d, string p) {
        name = n;
        duration = d;
        plan = p;
    }

    void showMembership() {
        cout << "Name: " << name << endl;

        if (duration == -1)
            cout << "Duration: Not Provided" << endl;
        else
            cout << "Duration: " << duration << " months" << endl;

        if (plan == "NA")
            cout << "Plan: Not Provided" << endl;
        else
            cout << "Plan: " << plan << endl;
    }
};

int main() {
    string name, plan;
    int duration;
    getline(cin, name);
    cin >> duration;
    cin.ignore();
    getline(cin, plan);
    if (duration == -1 && plan == "NA") {
        Membership m(name);
        m.showMembership();
    }
    else if (plan == "NA") {
        Membership m(name, duration);
        m.showMembership();
    }
    else {
        Membership m(name, duration, plan);
        m.showMembership();
    }
    return 0;
}

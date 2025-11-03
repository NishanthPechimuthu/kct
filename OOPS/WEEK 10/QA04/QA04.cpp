#include <iostream>
#include <string>
using namespace std;

class TravelPackage {
private:
    string destination;
    int days;
    string accommodation;

public:
    TravelPackage(string dest) {
        destination = dest;
        days = -1;
        accommodation = "NA";
    }

    TravelPackage(string dest, int d) {
        destination = dest;
        days = d;
        accommodation = "NA";
    }

    TravelPackage(string dest, int d, string acc) {
        destination = dest;
        days = d;
        accommodation = acc;
    }

    void display() {
        cout << "Destination: " << destination << endl;
        if (days == -1)
            cout << "Days: Not Provided" << endl;
        else
            cout << "Days: " << days << endl;

        if (accommodation == "NA")
            cout << "Accommodation: Not Provided" << endl;
        else
            cout << "Accommodation: " << accommodation << endl;
    }
};

int main() {
    string destination, accommodation;
    int days;

    getline(cin, destination);
    cin >> days;
    cin.ignore();
    getline(cin, accommodation);

    if (days == -1 && accommodation == "NA") {
        TravelPackage tp(destination);
        tp.display();
    }
    else if (accommodation == "NA") {
        TravelPackage tp(destination, days);
        tp.display();
    }
    else {
        TravelPackage tp(destination, days, accommodation);
        tp.display();
    }

    return 0;
}

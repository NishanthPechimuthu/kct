#include <iostream>
using namespace std;

int maxValue(int a, int b) {
    return (a > b) ? a : b;
}

int maxValue(int a, int b, int c) {
    return (a > b && a > c) ? a : (b > c ? b : c);
}

int main() {
    int a, b, c;
    string line;
    
    getline(cin, line);
    int count = 0;
    for (char ch : line) {
        if (ch == ' ') count++;
    }

    if (count == 1) {
        sscanf(line.c_str(), "%d %d", &a, &b);
        cout << maxValue(a, b);
    } 
    else if (count == 2) {
        sscanf(line.c_str(), "%d %d %d", &a, &b, &c);
        cout << maxValue(a, b, c);
    } 
    else {
        cout << "Invalid input";
    }

    return 0;
}

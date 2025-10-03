#include <iostream>
#include <string>
using namespace std;

class Teacher{
    protected:
        double basic_pay;
        void setSalary(double pay){
            basic_pay=pay;
        }
};

class Researcher{
    protected:
        double allowance;
        void getAllowance(string res){
            if(res=="ai"){
                allowance=6500;
            }
            else if(res=="quantum"){
                allowance=8750;
            }
            else{
                allowance=0;
            }
        }
};

class Professor:public Teacher, Researcher{
    public:
        double total;
        Professor(double sal, string resh){
            setSalary(sal);
            getAllowance(resh);
            totalPay();
        }
        void totalPay(){
            total=basic_pay+allowance;
            cout<<"Total Payment is "<<total<<endl;
        }
};

int main(){
    int salary;
    string res;
    cout<<"Enter a salary and research field:"<<endl;
    cin>>salary>>res;
    if(salary>0){
        Professor prof(salary, res);
    }

    return 0;
}

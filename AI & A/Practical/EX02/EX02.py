from sympy import symbols, Not, And, Implies

BL, GS, WC, PD = symbols('BL GS WC PD')
P, RTB, LI = symbols('P RTB LI')

rule1 = Implies(BL, RTB)
rule2 = Implies(PD, RTB)
rule3 = Implies(Not(WC), RTB)
rule4 = Implies(And(Not(BL), Not(GS)), LI)
rule5 = Implies(And(Not(BL), GS, WC, Not(PD)), P)

knowledge_base = [rule1, rule2, rule3, rule4, rule5]

def decide_drone_action(battery_low, gps_strong, weather_clear, package_delivered):
    facts = {BL: battery_low, GS: gps_strong, WC: weather_clear, PD: package_delivered}
    for rule in knowledge_base:
        lhs, rhs = rule.args
        if lhs.subs(facts):
            if rhs == RTB:
                return "Return to Base"
            elif rhs == LI:
                return "Land Immediately"
            elif rhs == P:
                return "Proceed"
    return "Return to Base"

print(decide_drone_action(False, True, True, False))
print(decide_drone_action(True, True, True, False))
print(decide_drone_action(False, False, True, False))
print(decide_drone_action(False, True, False, False))
print(decide_drone_action(False, True, True, True))

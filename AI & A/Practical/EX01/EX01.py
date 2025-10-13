from sympy import symbols, And, Not, Implies, satisfiable

D, W, M, H, A = symbols("Door Window Motion Home Alarm")

rule1 = Implies(And(D, M), A)
rule2 = Implies(W, A)
rule3 = Implies(And(M, Not(H)), A)
KB = And(rule1, rule2, rule3)

def check_alarm(door, window, motion, home):
    situation = And(
        D if door else Not(D),
        W if window else Not(W),
        M if motion else Not(M),
        H if home else Not(H)
    )
    test = And(KB, situation, Not(A))
    alarm = not satisfiable(test)
    print(f"Door={door}, Window={window}, Motion={motion}, Home={home} → Alarm={alarm}")
    return alarm

check_alarm(True, False, True, False)
check_alarm(False, True, False, True)
check_alarm(False, False, True, False)
check_alarm(False, False, True, True)
check_alarm(False, False, False, True)

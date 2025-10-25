from sympy import symbols, Eq, solve, Matrix

def mdp_sympy():
    print("\n--- Markov Decision Process ---")
    
    S0, S1 = symbols('S0 S1')
    A0, A1 = symbols('A0 A1')
    
    P_S0_A0_S0 = symbols('P_S0_A0_S0')
    P_S1_A0_S0 = 1 - P_S0_A0_S0
    P_S0_A1_S1 = symbols('P_S0_A1_S1')
    P_S1_A1_S1 = 1 - P_S0_A1_S1
    
    R_S0_A0 = symbols('R_S0_A0')
    R_S1_A1 = symbols('R_S1_A1')
    
    gamma = symbols('gamma')
    
    V0, V1 = symbols('V0 V1')
    eq1 = Eq(V0, R_S0_A0 + gamma*(P_S0_A0_S0*V0 + P_S1_A0_S0*V1))
    eq2 = Eq(V1, R_S1_A1 + gamma*(P_S0_A1_S1*V0 + P_S1_A1_S1*V1))
    
    print("Bellman equations for two states and actions:")
    print(eq1)
    print(eq2)
    
    sol = solve([eq1, eq2], (V0, V1))
    print("\nSymbolic solution for value functions:")
    print(sol)

def game_theory_sympy():
    print("\n--- Game Theory: 2x2 Normal Form Game ---")
    
    R_top, R_bottom = symbols('R_top R_bottom')
    C_left, C_right = symbols('C_left C_right')
    
    a, b, c, d = symbols('a b c d')  
    e, f, g, h = symbols('e f g h')  
    
    U_top = R_top*(a*C_left + b*C_right) + R_bottom*(c*C_left + d*C_right)
    U_bottom = R_top*(c*C_left + d*C_right) + R_bottom*(a*C_left + b*C_right)  
    
    p, q = symbols('p q')  

    U_R_Top = p*(a*q + b*(1-q)) + (1-p)*(c*q + d*(1-q))
    U_R_Bottom = p*(c*q + d*(1-q)) + (1-p)*(a*q + b*(1-q))
    
    eq = Eq(U_R_Top, U_R_Bottom)
    sol = solve(eq, q)
    print("Mixed strategy equilibrium probability for Column player (q):")
    print(sol)

while True:
    print("\n1. Markov Decision Process (MDP)\n2. Game Theory (2x2)\n3. Exit")
    choice = input("Choose option: ")
    
    if choice == "1":
        mdp_sympy()
    elif choice == "2":
        game_theory_sympy()
    elif choice == "3":
        break
    else:
        print("Invalid option!")

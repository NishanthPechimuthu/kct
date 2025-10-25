from sympy import symbols, Eq, solve, Matrix

# --------- Markov Decision Process (MDP) ---------
def mdp_sympy():
    print("\n--- Markov Decision Process ---")
    
    # Define states S0, S1 and actions A0, A1
    S0, S1 = symbols('S0 S1')
    A0, A1 = symbols('A0 A1')
    
    # Transition probabilities: P(s'|s,a)
    P_S0_A0_S0 = symbols('P_S0_A0_S0')
    P_S1_A0_S0 = 1 - P_S0_A0_S0
    P_S0_A1_S1 = symbols('P_S0_A1_S1')
    P_S1_A1_S1 = 1 - P_S0_A1_S1
    
    # Rewards for each action at state
    R_S0_A0 = symbols('R_S0_A0')
    R_S1_A1 = symbols('R_S1_A1')
    
    # Discount factor
    gamma = symbols('gamma')
    
    # Value function equations (Bellman equations)
    V0, V1 = symbols('V0 V1')
    eq1 = Eq(V0, R_S0_A0 + gamma*(P_S0_A0_S0*V0 + P_S1_A0_S0*V1))
    eq2 = Eq(V1, R_S1_A1 + gamma*(P_S0_A1_S1*V0 + P_S1_A1_S1*V1))
    
    print("Bellman equations for two states and actions:")
    print(eq1)
    print(eq2)
    
    # Solve for symbolic value functions
    sol = solve([eq1, eq2], (V0, V1))
    print("\nSymbolic solution for value functions:")
    print(sol)

# --------- Game Theory (2x2 Normal Form Game) ---------
def game_theory_sympy():
    print("\n--- Game Theory: 2x2 Normal Form Game ---")
    
    # Players: Row (R) and Column (C)
    # Strategies: Top/Bottom (Row), Left/Right (Column)
    R_top, R_bottom = symbols('R_top R_bottom')
    C_left, C_right = symbols('C_left C_right')
    
    # Payoff matrices (symbols)
    a, b, c, d = symbols('a b c d')  # Row player payoffs
    e, f, g, h = symbols('e f g h')  # Column player payoffs
    
    # Row player expected payoff for Top and Bottom
    U_top = R_top*(a*C_left + b*C_right) + R_bottom*(c*C_left + d*C_right)
    U_bottom = R_top*(c*C_left + d*C_right) + R_bottom*(a*C_left + b*C_right)  # general formula
    
    # For simplicity, assume mixed strategy equilibrium: set derivatives to 0
    p, q = symbols('p q')  # probabilities of Row choosing Top, Column choosing Left
    # Row expected payoff
    U_R_Top = p*(a*q + b*(1-q)) + (1-p)*(c*q + d*(1-q))
    U_R_Bottom = p*(c*q + d*(1-q)) + (1-p)*(a*q + b*(1-q))
    
    # Set indifference condition for Row player
    eq = Eq(U_R_Top, U_R_Bottom)
    sol = solve(eq, q)
    print("Mixed strategy equilibrium probability for Column player (q):")
    print(sol)

# --------- Menu ---------
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

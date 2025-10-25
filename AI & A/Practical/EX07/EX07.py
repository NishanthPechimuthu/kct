from sympy import symbols, Eq, solve

def bayesian_network_sympy():

    R, S, W = symbols('R S W')

    P_R1 = 0.3  
    P_R0 = 0.7  
    P_S1 = 0.4  
    P_S0 = 0.6  

    P_W_given_RS = {
        (1, 1): 0.99,
        (1, 0): 0.9,
        (0, 1): 0.1,
        (0, 0): 0.0
    }

    rain = int(input("Evidence: Rain (0-No,1-Yes)? "))
    sprinkler = int(input("Evidence: Sprinkler (0-No,1-Yes)? "))

    prob_W1 = P_W_given_RS[(rain, sprinkler)]
    prob_W0 = 1 - prob_W1

    print(f"\nP(WetGrass=Yes | Rain={rain}, Sprinkler={sprinkler}) = {prob_W1}")
    print(f"P(WetGrass=No  | Rain={rain}, Sprinkler={sprinkler}) = {prob_W0}")

def hmm_sympy():
    R, S = symbols('R S')
    
    W, Sh, C = symbols('W Sh C')

    P_RR = 0.7
    P_RS = 0.3
    P_SR = 0.4
    P_SS = 0.6

    P_W_R = 0.1
    P_Sh_R = 0.4
    P_C_R = 0.5
    P_W_S = 0.6
    P_Sh_S = 0.3
    P_C_S = 0.1

    obs_input = input("Enter observation sequence (W=0, Sh=1, C=2) comma separated: ")
    obs_seq = [int(x) for x in obs_input.split(",")]

    obs_map = {0: W, 1: Sh, 2: C}

    first_obs = obs_map[obs_seq[0]]
    P_R = 0.6
    P_S = 0.4

    if first_obs == W:
        prob_R = P_R * P_W_R
        prob_S = P_S * P_W_S
    elif first_obs == Sh:
        prob_R = P_R * P_Sh_R
        prob_S = P_S * P_Sh_S
    else:
        prob_R = P_R * P_C_R
        prob_S = P_S * P_C_S

    total = prob_R + prob_S
    prob_R /= total
    prob_S /= total

    print(f"\nAfter first observation {first_obs}:")
    print(f"P(Rainy) = {prob_R}")
    print(f"P(Sunny) = {prob_S}")
    print("\nFurther steps can be computed recursively using Bayes rule symbolically.")

while True:
    print("\n1. Bayesian Network (SymPy)\n2. Hidden Markov Model (SymPy)\n3. Exit")
    choice = input("Choose option: ")
    
    if choice == "1":
        bayesian_network_sympy()
    elif choice == "2":
        hmm_sympy()
    elif choice == "3":
        break
    else:
        print("Invalid option!")

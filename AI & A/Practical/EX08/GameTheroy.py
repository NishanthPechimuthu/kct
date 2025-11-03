import nashpy as nash
import numpy as np

A = np.array([[3, 0],
              [5, 1]])  
B = np.array([[3, 5],
              [0, 1]])  

game = nash.Game(A, B)

equilibria = game.support_enumeration()

print("=== Nash Equilibria ===")
for i, (sigma, tau) in enumerate(equilibria, 1):
    print(f"\nEquilibrium {i}:")
    print(f"  Row player    : {sigma}")
    print(f"  Column player : {tau}")
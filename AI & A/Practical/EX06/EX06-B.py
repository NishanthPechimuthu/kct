import itertools

def n_queens(N):
    positions = []
    for perm in itertools.permutations(range(N)):
        if all(abs(perm[i] - perm[j]) != abs(i-j) for i in range(N) for j in range(i+1, N)):
            positions.append(perm)
    return positions

def print_n_queens(pos, N):
    for p in pos:
        print("+" + "---+"*N)
        for i in range(N):
            row = "|"
            for j in range(N):
                row += " Q |" if p[i] == j else "   |"
            print(row)
            print("+" + "---+"*N)
        print()

N = int(input("Enter number of queens: "))
solutions = n_queens(N)
print(f"\nTotal solutions: {len(solutions)}\n")
print_n_queens(solutions, N)

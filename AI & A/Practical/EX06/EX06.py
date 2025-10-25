import itertools

# --------- Sudoku Solver (Backtracking) ---------
def is_safe_sudoku(grid, row, col, num):
    for x in range(9):
        if grid[row][x] == num or grid[x][col] == num:
            return False
    start_row, start_col = 3*(row//3), 3*(col//3)
    for i in range(3):
        for j in range(3):
            if grid[start_row+i][start_col+j] == num:
                return False
    return True

def solve_sudoku(grid):
    for row in range(9):
        for col in range(9):
            if grid[row][col] == 0:
                for num in range(1, 10):
                    if is_safe_sudoku(grid, row, col, num):
                        grid[row][col] = num
                        if solve_sudoku(grid):
                            return True
                        grid[row][col] = 0
                return False
    return True

def print_sudoku(grid):
    print("+-------+-------+-------+")
    for i in range(9):
        print("| ", end="")
        for j in range(9):
            val = str(grid[i][j]) if grid[i][j] != 0 else "."
            print(val + " ", end="")
            if (j+1) % 3 == 0:
                print("| ", end="")
        print()
        if (i+1) % 3 == 0:
            print("+-------+-------+-------+")

# --------- N-Queens Solver ---------
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

# --------- Menu ---------
while True:
    print("\n1. Sudoku\n2. N-Queens\n3. Exit")
    choice = input("Choose option: ")
    
    if choice == "1":
        sudoku_grid = [
            [5, 3, 0, 0, 7, 0, 0, 0, 0],
            [6, 0, 0, 1, 9, 5, 0, 0, 0],
            [0, 9, 8, 0, 0, 0, 0, 6, 0],
            [8, 0, 0, 0, 6, 0, 0, 0, 3],
            [4, 0, 0, 8, 0, 3, 0, 0, 1],
            [7, 0, 0, 0, 2, 0, 0, 0, 6],
            [0, 6, 0, 0, 0, 0, 2, 8, 0],
            [0, 0, 0, 4, 1, 9, 0, 0, 5],
            [0, 0, 0, 0, 8, 0, 0, 7, 9]
        ]
        print("\nSudoku Question:")
        print_sudoku(sudoku_grid)
        if solve_sudoku(sudoku_grid):
            print("\nSolved Sudoku:")
            print_sudoku(sudoku_grid)
        else:
            print("\nNo solution exists!")

    elif choice == "2":
        N = int(input("Enter number of queens: "))
        solutions = n_queens(N)
        print(f"\nTotal solutions: {len(solutions)}\n")
        print_n_queens(solutions, N)
    
    elif choice == "3":
        break
    else:
        print("Invalid option!")

board = [" "] * 9

def print_board():
    for i in range(0, 9, 3):
        print(board[i], "|", board[i+1], "|", board[i+2])
        if i < 6:
            print("---------")

def check_winner(b, player):
    wins = [
        (0, 1, 2), (3, 4, 5), (6, 7, 8),
        (0, 3, 6), (1, 4, 7), (2, 5, 8),
        (0, 4, 8), (2, 4, 6)
    ]
    return any(b[a] == b[b1] == b[c] == player for a, b1, c in wins)

def minimax(b, is_maximizing):
    if check_winner(b, "O"):
        return 1
    if check_winner(b, "X"):
        return -1
    if " " not in b:
        return 0

    if is_maximizing:
        best = -2
        for i in range(9):
            if b[i] == " ":
                b[i] = "O"
                score = minimax(b, False)
                b[i] = " "
                best = max(best, score)
        return best
    else:
        best = 2
        for i in range(9):
            if b[i] == " ":
                b[i] = "X"
                score = minimax(b, True)
                b[i] = " "
                best = min(best, score)
        return best

def best_move():
    best_score = -2
    move = 0
    for i in range(9):
        if board[i] == " ":
            board[i] = "O"
            score = minimax(board, False)
            board[i] = " "
            if score > best_score:
                best_score = score
                move = i
    return move

while True:
    print_board()
    if check_winner(board, "X") or check_winner(board, "O") or " " not in board:
        break

    pos = int(input("Enter position (0-8): "))
    if board[pos] == " ":
        board[pos] = "X"
    else:
        print("Position already taken! Try again.")
        continue

    if check_winner(board, "X") or " " not in board:
        break

    board[best_move()] = "O"

print_board()

if check_winner(board, "X"):
    print("You Win!")
elif check_winner(board, "O"):
    print("AI Wins!")
else:
    print("It's a Draw!")

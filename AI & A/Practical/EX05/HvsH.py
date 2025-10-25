import pygame
import sys

pygame.init()
screen = pygame.display.set_mode((300, 350))
pygame.display.set_caption("Simple Tic Tac Toe")
font = pygame.font.Font(None, 60)
small_font = pygame.font.Font(None, 40)

board = [["" for _ in range(3)] for _ in range(3)]
turn = "X"
winner = None

def draw_board():
    screen.fill((255, 255, 255))
    for i in range(1, 3):
        pygame.draw.line(screen, (0, 0, 0), (0, i * 100), (300, i * 100), 3)
        pygame.draw.line(screen, (0, 0, 0), (i * 100, 0), (i * 100, 300), 3)

    for r in range(3):
        for c in range(3):
            text = font.render(board[r][c], True, (0, 0, 0))
            screen.blit(text, (c * 100 + 35, r * 100 + 25))

    if winner:
        text = small_font.render(f"{winner} wins! Press R", True, (255, 0, 0))
    else:
        text = small_font.render(f"Turn: {turn}", True, (0, 0, 255))
    screen.blit(text, (60, 310))

def check_winner():
    for row in board:
        if row[0] == row[1] == row[2] != "":
            return row[0]
    for c in range(3):
        if board[0][c] == board[1][c] == board[2][c] != "":
            return board[0][c]
    if board[0][0] == board[1][1] == board[2][2] != "":
        return board[0][0]
    if board[0][2] == board[1][1] == board[2][0] != "":
        return board[0][2]
    return None

running = True
while running:
    for event in pygame.event.get():
        if event.type == pygame.QUIT:
            running = False
        elif event.type == pygame.KEYDOWN and event.key == pygame.K_r:
            board = [["" for _ in range(3)] for _ in range(3)]
            turn = "X"
            winner = None
        elif event.type == pygame.MOUSEBUTTONDOWN and not winner:
            x, y = pygame.mouse.get_pos()
            row, col = y // 100, x // 100
            if row < 3 and board[row][col] == "":
                board[row][col] = turn
                winner = check_winner()
                if not winner:
                    turn = "O" if turn == "X" else "X"

    draw_board()
    pygame.display.flip()

pygame.quit()
sys.exit()

import pygame, sys, random, chess

pygame.init()
screen = pygame.display.set_mode((600, 650))
pygame.display.set_caption("Game Hub - Tic Tac Toe & Chess")
font = pygame.font.Font(None, 60)
small_font = pygame.font.Font(None, 36)
piece_font = pygame.font.SysFont(None, 48)

menu = True
game = None

board = [["" for _ in range(3)] for _ in range(3)]
human = "X"
computer = "O"
winner = None
turn = human

chess_board = chess.Board()
sq_size = 60
offset = 60
selected_square = None

def draw_tic_board():
    screen.fill((255, 255, 255))
    for i in range(1, 3):
        pygame.draw.line(screen, (0, 0, 0), (150, i * 100 + 100), (450, i * 100 + 100), 3)
        pygame.draw.line(screen, (0, 0, 0), (i * 100 + 150, 100), (i * 100 + 150, 400), 3)
    for r in range(3):
        for c in range(3):
            txt = font.render(board[r][c], True, (0, 0, 0))
            screen.blit(txt, (c * 100 + 185, r * 100 + 125))
    if winner:
        msg = small_font.render(f"{winner} wins! Press R", True, (255, 0, 0))
    elif all(board[r][c] != "" for r in range(3) for c in range(3)):
        msg = small_font.render("Draw! Press R", True, (255, 0, 0))
    else:
        msg = small_font.render("Your turn (X)", True, (0, 0, 255))
    screen.blit(msg, (180, 550))

def check_winner(b):
    for r in range(3):
        if b[r][0] == b[r][1] == b[r][2] != "":
            return b[r][0]
    for c in range(3):
        if b[0][c] == b[1][c] == b[2][c] != "":
            return b[0][c]
    if b[0][0] == b[1][1] == b[2][2] != "":
        return b[0][0]
    if b[0][2] == b[1][1] == b[2][0] != "":
        return b[0][2]
    return None

def minimax(b, depth, isMax):
    win = check_winner(b)
    if win == computer:
        return 1
    if win == human:
        return -1
    if all(b[r][c] != "" for r in range(3) for c in range(3)):
        return 0
    if isMax:
        best = -999
        for r in range(3):
            for c in range(3):
                if b[r][c] == "":
                    b[r][c] = computer
                    best = max(best, minimax(b, depth + 1, False))
                    b[r][c] = ""
        return best
    else:
        best = 999
        for r in range(3):
            for c in range(3):
                if b[r][c] == "":
                    b[r][c] = human
                    best = min(best, minimax(b, depth + 1, True))
                    b[r][c] = ""
        return best

def best_move():
    bestVal = -999
    move = None
    for r in range(3):
        for c in range(3):
            if board[r][c] == "":
                board[r][c] = computer
                moveVal = minimax(board, 0, False)
                board[r][c] = ""
                if moveVal > bestVal:
                    bestVal = moveVal
                    move = (r, c)
    if move:
        board[move[0]][move[1]] = computer

def reset_tic():
    global board, winner, turn
    board = [["" for _ in range(3)] for _ in range(3)]
    winner = None
    turn = human

def draw_chess_board():
    colors = [(240, 217, 181), (181, 136, 99)]
    for r in range(8):
        for c in range(8):
            color = colors[(r + c) % 2]
            pygame.draw.rect(screen, color, pygame.Rect(c * sq_size + offset, r * sq_size + offset, sq_size, sq_size))
    if selected_square is not None:
        col = chess.square_file(selected_square)
        row = chess.square_rank(selected_square)
        draw_r = 7 - row
        draw_c = col
        highlight_rect = pygame.Rect(draw_c * sq_size + offset, draw_r * sq_size + offset, sq_size, sq_size)
        pygame.draw.rect(screen, (120, 200, 120), highlight_rect, 4)
    for r in range(8):
        for c in range(8):
            square = chess.square(c, 7 - r)
            piece = chess_board.piece_at(square)
            if piece:
                sym = piece.symbol()  # P,N,B,R,Q,K or lowercase
                txt = piece_font.render(sym, True, (0, 0, 0))
                txt_rect = txt.get_rect(center=(c * sq_size + offset + sq_size // 2, r * sq_size + offset + sq_size // 2 - 4))
                screen.blit(txt, txt_rect)
    msg = small_font.render("Press M for Menu", True, (0, 0, 255))
    screen.blit(msg, (190, 600))
    if chess_board.is_checkmate():
        over = small_font.render("Checkmate!", True, (255, 0, 0))
        screen.blit(over, (240, 20))
    elif chess_board.is_stalemate():
        over = small_font.render("Stalemate", True, (255, 0, 0))
        screen.blit(over, (250, 20))

def ai_chess_move():
    legal_moves = list(chess_board.legal_moves)
    if legal_moves:
        move = random.choice(legal_moves)
        chess_board.push(move)

def reset_chess():
    global chess_board, selected_square
    chess_board = chess.Board()
    selected_square = None

def draw_menu():
    screen.fill((240, 240, 240))
    title = font.render("Choose a Game", True, (0, 0, 0))
    screen.blit(title, (150, 80))
    tic_btn = pygame.Rect(200, 200, 200, 60)
    chess_btn = pygame.Rect(200, 300, 200, 60)
    pygame.draw.rect(screen, (0, 150, 255), tic_btn, border_radius=15)
    pygame.draw.rect(screen, (0, 0, 0), tic_btn, 3, border_radius=15)
    pygame.draw.rect(screen, (100, 100, 100), chess_btn, border_radius=15)
    pygame.draw.rect(screen, (0, 0, 0), chess_btn, 3, border_radius=15)
    screen.blit(small_font.render("Tic Tac Toe", True, (255, 255, 255)), (230, 215))
    screen.blit(small_font.render("Chess", True, (255, 255, 255)), (265, 315))
    return tic_btn, chess_btn

running = True
while running:
    for e in pygame.event.get():
        if e.type == pygame.QUIT:
            running = False

        if menu:
            if e.type == pygame.MOUSEBUTTONDOWN:
                tic_btn, chess_btn = draw_menu()
                x, y = e.pos
                if tic_btn.collidepoint(x, y):
                    menu = False
                    game = "tic"
                    reset_tic()
                elif chess_btn.collidepoint(x, y):
                    menu = False
                    game = "chess"
                    reset_chess()
        else:
            if game == "tic":
                if e.type == pygame.KEYDOWN and e.key == pygame.K_r:
                    reset_tic()
                elif e.type == pygame.KEYDOWN and e.key == pygame.K_m:
                    menu = True
                elif e.type == pygame.MOUSEBUTTONDOWN and turn == human and not winner:
                    x, y = pygame.mouse.get_pos()
                    if 150 < x < 450 and 100 < y < 400:
                        r, c = (y - 100) // 100, (x - 150) // 100
                        if board[r][c] == "":
                            board[r][c] = human
                            winner = check_winner(board)
                            if not winner and any(board[r][c] == "" for r in range(3) for c in range(3)):
                                turn = computer
                                best_move()
                                winner = check_winner(board)
                                turn = human

            elif game == "chess":
                if e.type == pygame.KEYDOWN and e.key == pygame.K_m:
                    menu = True
                elif not chess_board.is_game_over() and e.type == pygame.MOUSEBUTTONDOWN:
                    x, y = e.pos
                    if offset < x < offset + 8 * sq_size and offset < y < offset + 8 * sq_size:
                        col = (x - offset) // sq_size
                        row = 7 - (y - offset) // sq_size
                        square = chess.square(col, row)
                        piece = chess_board.piece_at(square)
                        if selected_square is None:
                            if piece and piece.color == chess.WHITE:
                                selected_square = square
                        else:
                            move = chess.Move(selected_square, square)
                            if move in chess_board.legal_moves:
                                chess_board.push(move)
                                if not chess_board.is_game_over():
                                    ai_chess_move()
                            selected_square = None

    if menu:
        draw_menu()
    elif game == "tic":
        draw_tic_board()
    elif game == "chess":
        draw_chess_board()

    pygame.display.flip()

pygame.quit()
sys.exit()

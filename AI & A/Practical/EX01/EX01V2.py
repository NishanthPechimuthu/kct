from collections import deque
import heapq 

maze = [
    ['S', '.', '#', '.', '.'],
    ['.', '#', '.', '.', '.'],
    ['.', '.', '.', '#', '.'],
    ['#', '.', '#', '.', 'G'],
    ['.', '.', '.', '.', '.']
]

rows, cols = len(maze), len(maze[0])
directions = [(-1, 0), (1, 0), (0, -1), (0, 1)]

def find_position(symbol):
    for r in range(rows):
        for c in range(cols):
            if maze[r][c] == symbol:
                return (r, c)
    return None

def is_valid(r, c, visited):
    return (0 <= r < rows and 0 <= c < cols
            and maze[r][c] != '#' and (r, c) not in visited)

def heuristic(a, b):
    return abs(a[0] - b[0]) + abs(a[1] - b[1])

def bfs(start, goal):
    queue = deque([(start, [start])])
    visited = set([start])
    while queue:
        (r, c), path = queue.popleft()
        if (r, c) == goal:
            return path
        for dr, dc in directions:
            nr, nc = r + dr, c + dc
            if is_valid(nr, nc, visited):
                visited.add((nr, nc))
                queue.append(((nr, nc), path + [(nr, nc)]))
    return None

def dfs(start, goal):
    stack = [(start, [start])]
    visited = set([start])
    while stack:
        (r, c), path = stack.pop()
        if (r, c) == goal:
            return path
        for dr, dc in directions:
            nr, nc = r + dr, c + dc
            if is_valid(nr, nc, visited):
                visited.add((nr, nc))
                stack.append(((nr, nc), path + [(nr, nc)]))
    return None

def a_star(start, goal):
    open_set = []
    heapq.heappush(open_set, (0 + heuristic(start, goal), 0, start, [start]))
    visited = set([start])
    
    while open_set:
        f, g, (r, c), path = heapq.heappop(open_set)
        if (r, c) == goal:
            return path
        for dr, dc in directions:
            nr, nc = r + dr, c + dc
            if is_valid(nr, nc, visited):
                visited.add((nr, nc))
                new_g = g + 1
                f_cost = new_g + heuristic((nr, nc), goal)
                heapq.heappush(open_set, (f_cost, new_g, (nr, nc), path + [(nr, nc)]))
    return None

start = find_position('S')
goal = find_position('G')

bfs_path = bfs(start, goal)
dfs_path = dfs(start, goal)
a_star_path = a_star(start, goal)

print("BFS Path:", bfs_path)
print("DFS Path:", dfs_path)
print("A* Path:", a_star_path)
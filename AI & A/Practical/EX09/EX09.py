import numpy as np
import matplotlib.pyplot as plt

states = ['S1', 'S2', 'S3']
actions = ['Left', 'Right']
gamma = 0.9

rewards = {
    ('S1', 'Right'): 1,
    ('S2', 'Right'): 10,
    ('S2', 'Left'): 0
}

transitions = {
    ('S1', 'Right'): 'S2',
    ('S2', 'Right'): 'S3',
    ('S2', 'Left'): 'S1'
}

V = {s: 0 for s in states}
policy = {s: None for s in states}

for _ in range(100):
    delta = 0
    for s in states:
        if s == 'S3':
            continue
        values = []
        for a in actions:
            if (s, a) in transitions:
                next_state = transitions[(s, a)]
                reward = rewards[(s, a)]
                values.append(reward + gamma * V.get(next_state, 0))
        if values:
            new_value = max(values)
            delta = max(delta, abs(V[s] - new_value))
            V[s] = new_value
    if delta < 1e-6:
        break

for s in states:
    if s == 'S3':
        policy[s] = '—'
        continue
    best_action = None
    best_value = -float('inf')
    for a in actions:
        if (s, a) in transitions:
            next_state = transitions[(s, a)]
            reward = rewards[(s, a)]
            val = reward + gamma * V.get(next_state, 0)
            if val > best_value:
                best_value = val
                best_action = a
    policy[s] = best_action

print("Final State Values:", V)
print("Optimal Policy:", policy)
plt.bar(V.keys(), V.values(), color='skyblue')
for s in states:
    plt.text(s, V[s] + 0.1, f'{policy[s]}', ha='center')
plt.title("State Values and Optimal Policy")
plt.xlabel("States")
plt.ylabel("Value")
plt.ylim(0, max(V.values()) + 2)
plt.show()



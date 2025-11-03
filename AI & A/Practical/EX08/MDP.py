import numpy as np
import mdptoolbox.mdp

P = np.array([                    
    [[0.8, 0.1, 0.1],            
     [0.0, 0.7, 0.3],
     [0.2, 0.2, 0.6]],

    [[0.1, 0.8, 0.1],             
     [0.3, 0.3, 0.4],
     [0.0, 0.6, 0.4]]
])

R = np.array([                     
    [5,  1],  
    [10, 5]   
])

vi = mdptoolbox.mdp.ValueIteration(P, R, 0.9)
vi.run()

print("Best action for each state:", vi.policy, vi.V)
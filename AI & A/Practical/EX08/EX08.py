import numpy as np
from hmmlearn import hmm
import matplotlib.pyplot as plt
import seaborn as sns

n_hidden_states = 2
n_observation_symbols = 3

model = hmm.MultinomialHMM(n_components=n_hidden_states, n_iter=100, random_state=42)

model.startprob_ = np.array([0.6, 0.4])
model.transmat_ = np.array([[0.7, 0.3],
                            [0.4, 0.6]])
model.emissionprob_ = np.array([[0.6, 0.3, 0.1],
                                [0.2, 0.4, 0.4]])

observations = np.array([[0, 1, 2, 1, 0, 2, 1, 0]]).T
model.fit(observations)

logprob, hidden_states = model.decode(observations, algorithm="viterbi")
print("Observed sequence:      ", observations.T[0])
print("Predicted hidden states:", hidden_states)

model.n_trials = 1
new_obs, new_states = model.sample(10)
print("\nGenerated Observations: ", new_obs.T[0])
print("Generated Hidden States:", new_states)

plt.figure(figsize=(10,4))
plt.subplot(1,2,1)
sns.heatmap(model.transmat_, annot=True, cmap="Blues", cbar=False)
plt.title("Transition Probabilities")
plt.xlabel("To State")
plt.ylabel("From State")

plt.subplot(1,2,2)
sns.heatmap(model.emissionprob_, annot=True, cmap="Greens", cbar=False)
plt.title("Emission Probabilities")
plt.xlabel("Observation Symbol")
plt.ylabel("Hidden State")

plt.tight_layout()
plt.show()

plt.figure(figsize=(8,2))
plt.plot(observations, 'o-', label='Observations')
plt.plot(hidden_states, 's--', label='Predicted States')
plt.legend()
plt.title("Observed vs Predicted Hidden States")
plt.show()

from pgmpy.models import BayesianNetwork 
from pgmpy.factors.discrete import TabularCPD 
from pgmpy.inference import VariableElimination 
# Define network structure 
model = BayesianNetwork([('Disease', 'Symptom1'), ('Disease', 'Symptom2')]) 
# Define Conditional Probability Distributions (CPDs) 
cpd_disease = TabularCPD('Disease', 2, [[0.1], [0.9]])  # P(Disease) 
cpd_s1 = TabularCPD('Symptom1', 2, [[0.8, 0.1], [0.2, 0.9]],  
evidence=['Disease'], evidence_card=[2])  # P(S1|D) 
cpd_s2 = TabularCPD('Symptom2', 2, [[0.7, 0.2], [0.3, 0.8]],  
evidence=['Disease'], evidence_card=[2])  # P(S2|D) 
# Add CPDs to model 
model.add_cpds(cpd_disease, cpd_s1, cpd_s2) 
# Perform inference 
infer = VariableElimination(model)
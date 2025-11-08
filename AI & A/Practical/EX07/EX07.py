from pgmpy.models import DiscreteBayesianNetwork
from pgmpy.factors.discrete import TabularCPD
from pgmpy.inference import VariableElimination
from pgmpy.sampling import BayesianModelSampling
import matplotlib.pyplot as plt
import networkx as nx

model = DiscreteBayesianNetwork([("Temperature", "Rain"), ("Rain", "Humidity")])

cpd_temp = TabularCPD(variable="Temperature", variable_card=3, values=[[0.3], [0.4], [0.3]])

cpd_rain = TabularCPD(
    variable="Rain", variable_card=2,
    values=[[0.8, 0.4, 0.2],
            [0.2, 0.6, 0.8]],
    evidence=["Temperature"], evidence_card=[3]
)

cpd_humidity = TabularCPD(
    variable="Humidity", variable_card=2,
    values=[[0.7, 0.3],
            [0.3, 0.7]],
    evidence=["Rain"], evidence_card=[2]
)

model.add_cpds(cpd_temp, cpd_rain, cpd_humidity)
model.check_model()

infer = VariableElimination(model)

print(infer.query(variables=["Rain"], evidence={"Temperature": 0}))
print(infer.map_query(variables=["Humidity"], evidence={"Rain": 1}))

sampler = BayesianModelSampling(model)
print(sampler.forward_sample(size=5))

G = nx.DiGraph()
G.add_edges_from(model.edges())
pos = nx.spring_layout(G)
nx.draw(G, pos, with_labels=True, node_size=3000, font_size=12)
plt.show()

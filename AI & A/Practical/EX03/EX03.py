from pyDatalog import pyDatalog

pyDatalog.clear()

pyDatalog.create_terms('Parent, Male, Female, Father, Mother, Sibling, Grandparent, X, Y, Z')

+Parent('John', 'Mary')
+Parent('Mary', 'Alice')
+Parent('John', 'Mark')
+Parent('Mark', 'Sam')

+Male('John')
+Male('Mark')
+Male('Sam')

+Female('Mary')
+Female('Alice')

Grandparent(X, Z) <= Parent(X, Y) & Parent(Y, Z)
Sibling(Y, Z) <= Parent(X, Y) & Parent(X, Z) & (Y != Z)
Father(X, Y) <= Parent(X, Y) & Male(X)
Mother(X, Y) <= Parent(X, Y) & Female(X)
Sibling(X, Y) <= Sibling(Y, X)

print("Grandparents of Alice:", Grandparent(X, 'Alice').data)
print("Siblings of Mary:", Sibling('Mary', X).data)
print("Fathers:", Father(X, Y).data)
print("Mothers:", Mother(X, Y).data)

print("Is John a Grandparent of Alice?", ('John',) in Grandparent(X, 'Alice').data)
print("Is Mary the Mother of Alice?", ('Mary',) in Mother(X, 'Alice').data)
import pandas as pd
import matplotlib.pyplot as plt

df = pd.read_csv('data.csv')
print(df)
print("Head")
print(df.head())
print("Tail")
print(df.tail())        
print("info")
print(df.info())
print("describe")
print(df.describe())        
plt.bar(df['StockCode'], df['Quantity'])
plt.show()

df['Sales'] = df['Quantity'] * df['UnitPrice']
sales_per_product = df.groupby('StockCode')['Sales'].sum().head(5)

plt.bar(sales_per_product.index, sales_per_product.values)
plt.xlabel('Product')
plt.ylabel('Sales')
plt.title('Sales per Product')
plt.xticks(rotation=45)
plt.show()
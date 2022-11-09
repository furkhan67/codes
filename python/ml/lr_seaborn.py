import seaborn as sns
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import warnings
#%matplotlib inline
sns.set(style="darkgrid", palette="hsv")
warnings.filterwarnings("ignore")


datas = pd.read_csv("datasets/insurance.csv")
sns.lmplot(x="X",y="Y1", data = datas)
plt.savefig("out.png")
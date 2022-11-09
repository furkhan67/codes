import sklearn
from sklearn.model_selection import train_test_split
import pandas
import numpy as np
import matplotlib
from matplotlib.pyplot import scatter
import seaborn
from sklearn.linear_model import LinearRegression
from sklearn.datasets import load_boston

datasets=load_boston()


boston=pandas.DataFrame(datasets.data)

boston.columns=datasets.feature_names

boston["PRICE"]=datasets.target
#print(datasets.DESCR)

X=boston.LSTAT
Y=boston.PRICE
X=np.array(X).reshape(-1,1)
Y=np.array(Y).reshape(-1,1)

X_train,X_test,Y_train,Y_test=train_test_split(X,Y,test_size=.2,random_state=5)
#print(X_train.head)
#print(Y_train.head)
#print(X_test.head)
#print(Y_test.head)

lm=LinearRegression() #creating linear regression model
lm.fit(X_train,Y_train) #training model

y1=lm.predict(X_train)
Y_pred = lm.predict(X_test) #predicting y from test_set

scatter(X,Y) #plotting sctter points as actual points
matplotlib.pyplot.xlabel("% decrease in population")
matplotlib.pyplot.ylabel("predicted prices")

#prediction_space = np.linspace(min(X), max(X)).reshape(-1,1)
matplotlib.pyplot.plot(X_test,Y_pred,color='black',linewidth = 3)

matplotlib.pyplot.show()

error=sklearn.metrics.mean_squared_error(Y_test,Y_pred)
print("Error = "+str(error))
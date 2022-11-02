from os import truncate
from pyspark.sql import SparkSession

spark = SparkSession \
    .builder \
    .appName("Logistic Regression Spark") \
    .config("spark.some.config.option", "some-value") \
    .getOrCreate()

dataset = spark.read.csv('diabetes.csv',inferSchema=True, header =True)

#print(dataset.columns)
#dataset.describe().select("*").show()

#count null and nan values in csv
from pyspark.sql.functions import col,isnan, when, count
print("*****nan values in dataset before replacing 'zeroes with nan'*****")
dataset.select([count(when(isnan(c) | col(c).isNull(), c)).alias(c) for c in dataset.columns]
   ).show()

# replace min value of zeros with Nan as data cleaning processs
import numpy as np
from pyspark.sql.functions import when
dataset=dataset.withColumn("Glucose",when(dataset.Glucose==0,np.nan).otherwise (dataset.Glucose))
dataset=dataset.withColumn("BloodPressure",when(dataset.BloodPressure==0,np.nan).otherwise(dataset.BloodPressure))
dataset=dataset.withColumn("SkinThickness",when(dataset.SkinThickness==0,np.nan).otherwise(dataset.SkinThickness))
dataset=dataset.withColumn("BMI",when(dataset.BMI==0,np.nan).otherwise(dataset.BMI))
dataset=dataset.withColumn("Insulin",when(dataset.Insulin==0,np.nan).otherwise(dataset.Insulin))
dataset.select("Insulin","Glucose","BloodPressure","SkinThickness","BMI").show(5)

# check again NAN values 
from pyspark.sql.functions import col,isnan, when, count
print("*****nan values in dataset before replacing 'zeroes with nan'*****")
dataset.select([count(when(isnan(c) , c)).alias(c) for c in dataset.columns]
   ).show()


#The Imputer estimator completes missing values in a dataset, either using the mean or the median of the columns in which the missing values are located.
#impute
from pyspark.ml.feature import Imputer
imputer=Imputer(inputCols=["Glucose","BloodPressure","SkinThickness","BMI","Insulin"],outputCols=["Glucose","BloodPressure","SkinThickness","BMI","Insulin"])
model=imputer.fit(dataset)
dataset=model.transform(dataset)
dataset.show(5)


#combine all the features in one single feature vector.
cols=dataset.columns
cols.remove("Outcome")
# Let us import the vector assembler
from pyspark.ml.feature import VectorAssembler
assembler = VectorAssembler(inputCols=cols,outputCol="features")
# Now let us use the transform method to transform our dataset
dataset=assembler.transform(dataset)
#print(dataset.columns)
dataset.select("features").show(truncate=False)


#Standard Sclarizer
from pyspark.ml.feature import StandardScaler
standardscaler=StandardScaler().setInputCol("features").setOutputCol("Scaled_features")
dataset=standardscaler.fit(dataset).transform(dataset)
dataset.select("features","Scaled_features").show(5,truncate=False)

#Train, test split
train, test = dataset.randomSplit([0.8, 0.2], seed=12345)

#imbalance in the dataset, observe the use of Where
dataset_size=float(train.select("Outcome").count())
numPositives=train.select("Outcome").where('Outcome == 1').count()
per_ones=(float(numPositives)/float(dataset_size))
numNegatives=float(dataset_size-numPositives)
print('The number of ones are {}'.format(numPositives))
print('Ratio of ones to zeroes is {}'.format(per_ones))
print('The number of zeroes are {}'.format(numNegatives))

BalancingRatio= numNegatives/dataset_size
print('BalancingRatio = {}'.format(BalancingRatio))

# balance 
train=train.withColumn("classWeights", when(train.Outcome == 1,BalancingRatio).otherwise(1-BalancingRatio))
print(train.columns)

 #Feature selection
# Feature selection using chisquareSelector
from pyspark.ml.feature import ChiSqSelector
css = ChiSqSelector(featuresCol='Scaled_features',outputCol='Aspect',labelCol='Outcome',fpr=0.05)
print(type(css))
train=css.fit(train).transform(train)
test=css.fit(test).transform(test)
test.select("Aspect").show(5,truncate=False)


#Building a classification model using Logistic Regression (LR)
from pyspark.ml.classification import LogisticRegression
lr = LogisticRegression(labelCol="Outcome", featuresCol="Aspect",weightCol="classWeights",maxIter=10)
model=lr.fit(train)
predict_train=model.transform(train)
predict_test=model.transform(test)
predict_test.select("Outcome","prediction").show(10)

#Evaluating the model
from pyspark.ml.evaluation import BinaryClassificationEvaluator

evaluator=BinaryClassificationEvaluator(rawPredictionCol='prediction',labelCol="Outcome")
# We have only two choices: area under ROC and PR curves :-(
auroc = evaluator.evaluate(predict_test, {evaluator.metricName: "areaUnderROC"})

print("Area under ROC Curve: {:.4f}".format(auroc))

predict_test.select("Outcome","prediction","probability").show(15)


print(model.summary)
import matplotlib.pyplot as plt
pr = model.summary.pr.toPandas()
plt.plot(pr['recall'],pr['precision'])
plt.ylabel('Precision')
plt.xlabel('Recall')

print("Model Accuracy",model.summary.accuracy)
print("FP rate",model.summary.falsePositiveRateByLabel)
print("TR rate",model.summary.truePositiveRateByLabel)
plt.show()


print("Total Actual Positive i.e. diabetes",predict_test.select("Outcome").where('Outcome == 1.0').count())
print("Total Actual Negative,i.e. without diabetes",predict_test.select("Outcome").where('Outcome == 0.0').count())
pr = predict_test.toPandas()
TruePositive =0
FalsePositive=0
TrueNegative=0
FalseNegative=0
Postive=1.0
Negative=0.0
pos=0
Neg=0

print("Total",len(pr["Outcome"]))
for lbl in range(len(pr["Outcome"])):
  if  pr["prediction"][lbl]==Postive:
    pos+=1
    if pr["prediction"][lbl]==pr["Outcome"][lbl]:
      TruePositive+=1
    else:
      FalsePositive+=1
  if  pr["prediction"][lbl]==Negative:
    Neg+=1
    if pr["prediction"][lbl]==pr["Outcome"][lbl]:
      TrueNegative+=1
    else:
      FalseNegative+=1
print("Total Positive & Negative predicted,  diabetes: ",pos,",Non Diabetes",Neg)
print("TruePostive",TruePositive,"FalsePostive",FalsePositive)     
print("TrueNegative",TrueNegative,"FalseNegative",FalseNegative)
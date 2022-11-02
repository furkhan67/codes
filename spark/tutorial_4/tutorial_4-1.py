'''
This code is for classifying different species of flowers i.e setosa, versicolor, virginica
on basis of sep's and  petals' len and width
We can use two different techniques for this purpose Logistic regression and Decision tree. We used both below:

'''


from os import truncate
from pyspark.sql import SparkSession
spark = SparkSession.builder.master("local[*]").getOrCreate()

dataset = spark.read.csv('bezdekIris.csv',inferSchema=True, header =True)\
.toDF("sep_len", "sep_wid", "pet_len", "pet_wid", "label")
dataset.select('label').distinct().show(10)
print(dataset.count())


from pyspark.ml.feature import VectorAssembler
vector_assembler = VectorAssembler(inputCols=["sep_len", "sep_wid", "pet_len", "pet_wid"], outputCol="features")
df_temp = vector_assembler.transform(dataset)
df_temp.show(3)

#Let’s remove unnecessary columns:
df = df_temp.drop('sep_len', 'sep_wid', 'pet_len', 'pet_wid')
df.show(3)

#indexing label
from pyspark.ml.feature import StringIndexer
l_indexer = StringIndexer(inputCol="label", outputCol="labelIndex")
df = l_indexer.fit(df).transform(df)
df.select('label','labelIndex').distinct().show()

#using decision tree method for multiclassification
(trainingData, testData) = df.randomSplit([0.7, 0.3])
from pyspark.ml.classification import DecisionTreeClassifier
from pyspark.ml.evaluation import MulticlassClassificationEvaluator
dt = DecisionTreeClassifier(labelCol="labelIndex", featuresCol="features",impurity='entropy', maxDepth=4,seed=1234)
model = dt.fit(trainingData)
predictions = model.transform(testData)

#evaluating dt method
evaluator = MulticlassClassificationEvaluator(\
labelCol="labelIndex", predictionCol="prediction",\
metricName="accuracy")
accuracy = evaluator.evaluate(predictions)
print("Test accuracy =  " , accuracy)
print(model.toDebugString)

predictions.show(truncate=False)


# this is code for multiple classification using logistic Regression 
from pyspark.ml.classification import OneVsRest
from pyspark.ml.classification import LogisticRegression
train, test = df.randomSplit([0.7, 0.3], seed = 2018)
lr = LogisticRegression(maxIter=100,featuresCol="features", labelCol='labelIndex')
ovr = OneVsRest(classifier=lr,labelCol='labelIndex', featuresCol='features')
#from pyspark.ml import Pipeline
#pipeline_ovr = Pipeline(stages=[vecAssembler, stdScaler, ovr])
#pipelineModel_ovr = pipeline_ovr.fit(trainDF)

ovrModel = ovr.fit(train)
predictionsovr = ovrModel.transform(test)
evaluator = MulticlassClassificationEvaluator(labelCol="labelIndex", predictionCol="prediction", metricName="accuracy")
accuracy = evaluator.evaluate(predictionsovr)
print("Test accuracy =  " , accuracy)
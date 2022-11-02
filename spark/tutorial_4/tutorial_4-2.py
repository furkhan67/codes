'''
Try to group points into cluster on the basis of longitude and lattitude using kmeans clustering

'''


# start the spark only three steps

from pyspark.sql import SparkSession

spark = SparkSession.builder\
        .master("local")\
        .appName("ClusteringApp")\
        .enableHiveSupport()\
        .config('spark.ui.port', '4050')\
        .getOrCreate()


df = spark.read.csv('uber.csv',inferSchema=True, header =True)
df.show(5)

from pyspark.ml.feature import VectorAssembler
cols = ["Lat", "Lon"]

assembler=VectorAssembler(inputCols=cols, outputCol="features")
featureDf = assembler.transform(df)
featureDf.printSchema()
featureDf.show(10)

featureDf.count()
trainingData, testData = featureDf.randomSplit([0.7, 0.3], seed = 5043)

print("Training Dataset Count: " + str(trainingData.count()))

print("Test Dataset Count: " + str(testData.count()))

from pyspark.ml.clustering import KMeans
from pyspark.ml.evaluation import ClusteringEvaluator
'''

checking how many clusters gives most accuracy 
 

for i in range(2,10):
    kmeans = KMeans().setK(i).setSeed(1).setFeaturesCol("features").setPredictionCol("prediction")
    kmeansModel = kmeans.fit(trainingData)
    
    for clusters in kmeansModel.clusterCenters():
        print(clusters)
    

    predictDf = kmeansModel.transform(testData)
    #predictDf.show(10)
    evaluator = ClusteringEvaluator()  
    silhouette = evaluator.evaluate(predictDf)
    print("Silhouette with squared euclidean distance for  "+str(i)+" groups/clusters = " + str(silhouette))
    
    predictDf.groupBy("prediction").count().show()'''

#In this case 2 has highest accuracy hence using k=2
kmeans = KMeans().setK(2).setSeed(1).setFeaturesCol("features").setPredictionCol("prediction")
kmeansModel = kmeans.fit(trainingData)
    
for clusters in kmeansModel.clusterCenters():
    print(clusters)
    

predictDf = kmeansModel.transform(testData)
#predictDf.show(10)
evaluator = ClusteringEvaluator()  
silhouette = evaluator.evaluate(predictDf)
print("Silhouette with squared euclidean distance for 2 groups/clusters = " + str(silhouette))
    
predictDf.groupBy("prediction").count().show()


pddf_pred = predictDf.toPandas()
import matplotlib.pyplot as plt
pddf_pred.head()
fig = plt.figure()
KmVis= fig.add_subplot(111)
#threedee = plt.figure(figsize=(12,10))
KmVis.scatter(pddf_pred.Lat, pddf_pred.Lon,  c=pddf_pred.prediction,s=20)
KmVis.set_xlabel('x')
KmVis.set_ylabel('y')


plt.show()



#save model
kmeansModel.write().overwrite().save("uber-model")
#loading model
kmeansModelLoaded = kmeansModel.load("uber-model")
#creating dummy data 
df1 = spark.sparkContext.parallelize([
    ("5/1/2014 0:02:00", 40.7521, -73.9914, "B02512"),
    ("5/1/2014 0:06:00", 40.6965, -73.9715, "B02512"),
    ("5/1/2014 0:15:00", 40.7464, -73.9838, "B02512"),
    ("5/1/2014 0:17:00", 40.7463, -74.0011, "B02512"),
    ("5/1/2014 0:17:00", 40.7594, -73.9734, "B02512")]
  ).toDF(["time", "Lat", "Lon", "base"])
df1.show()

#creating feature vector

df2 = assembler.transform(df1)
df2.show()

# prediction of dummy data set with loaded model
df3 = kmeansModelLoaded.transform(df2)
df3.show()


# %%

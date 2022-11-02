#find spark installation
import findspark
findspark.init()

#initialize spark
from pyspark import SparkContext, SparkConf, StorageLevel
SparkConf=SparkConf().setAppName("tutorial1").setMaster("local[4]")
sc=SparkContext(conf = SparkConf)
print("No. of workers: "+str(sc.defaultParallelism))#see the no. of workers

#create RDD
data=[1,2,3,4,5,6]
rdd=sc.parallelize(data,4)#distribute data into 4 groups

print("No. of partitions: "+str(rdd.getNumPartitions()))#print no. of partitions
print("All the distributed partitions of data: "+str(rdd.glom().collect()))#print all the distributed groups of data

print("3rd partition: "+str(rdd.glom().collect()[3]))#print 3rd partition
print("First 2 partitions: "+str(rdd.glom().take(2)))#print 2 partitions

# count():
print("No of elements in rdd: "+str(rdd.count()))

# first():
print("First element: "+str(rdd.first()))

# top():
print("Top 2 elements: "+str(rdd.top(2)))

# distinct():
print("Distinct elements: "+str(rdd.distinct().collect()))

# map():
print("Multiply each element by 4 using map: "+str(rdd.map(lambda x:x*4).collect()))

# filter(): 
filter=rdd.filter(lambda x : x%2 == 0).collect()
print("Even elements only using filter: "+str(filter))

# caching policy:
print("***Now Caching rdd into memory***")
rdd.persist(StorageLevel.MEMORY_ONLY) #can use rdd.cache() instead as well
"""In the persist() method, we can use various storage levels
 such as MEMORY_ONLY, MEMORY_AND_DISK, MEMORY_ONLY_SER, MEMORY_AND_DISK_SER, DISK_ONLY.
 ex: rdd.persist(pyspark.StorageLevel.MEMORY_ONLY)

 Use df.unpersist() to remove rdd from persisted list
 """

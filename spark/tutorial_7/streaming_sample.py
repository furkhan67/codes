# Load Spark engine
import findspark
findspark.init()

# Import libs
import sys
from pyspark import SparkContext
from pyspark.streaming import StreamingContext

# Begin
def main():
        sc = SparkContext(appName="StreamingreduceByWindow");
        # The batch interval : 1 second
        ssc = StreamingContext(sc, 1)

	# Level of logging
        sc.setLogLevel("ERROR")

        # Checkpoint for backups
        ssc.checkpoint("checkpoint")
        
        lines = ssc.socketTextStream("localhost", 7000)
        
        lines = lines.flatMap(lambda line: line.split(" "))
        result = lines.reduceByWindow(lambda x,y: int(x)+ int(y), '', 10, 5)
        

        ## The program will run until manual termination
        result.pprint()
        ssc.start()
        ssc.awaitTermination()



if __name__ == '__main__':
	main()


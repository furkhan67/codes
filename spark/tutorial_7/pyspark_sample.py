# Load Spark engine
import findspark
findspark.init()

# Linking with Spark
from pyspark import SparkContext, SparkConf

# main function
if __name__ == "__main__":
    conf = SparkConf().setAppName("reduce").setMaster("local[*]")
    sc = SparkContext(conf = conf)
   
    inputIntegers = [1, 2, 3, 4, 5]
    integerRdd = sc.parallelize(inputIntegers)
   
    product = integerRdd.reduce(lambda x, y: x * y)
    print("product is :{}".format(product))
    

    #from tkinter import messagebox
    #messagebox.showinfo("message","product is :{}".format(product))


    # importing only  those functions which are needed 
    from tkinter import * 
   # from tkinter.ttk import * 
   
	  
    # creating tkinter window 
    root = Tk(className='PySpark app') 
  
    # setting the minimum size of the root window
    root.minsize(500, 200) 
  
    # Adding widgets to the root window 
    Label(root, text = product,font =('arial', 40)).pack(side = TOP, pady = 10)  
    mainloop()

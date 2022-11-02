# Big Data

Big data can be described in terms of data management challenges that – due to increasing volume, velocity and variety of data – cannot be solved with traditional databases. While there are plenty of definitions for big data, most of them include the concept of what’s commonly known as “three V’s” of big data:

**Volume**: Ranges from terabytes to petabytes of data

**Variety**: Includes data from a wide range of sources and formats (e.g. web logs, social media interactions, ecommerce and online transactions, financial transactions, etc)

**Velocity**: Increasingly, businesses have stringent requirements from the time data is generated, to the time actionable insights are delivered to the users. Therefore, data needs to be collected, stored, processed, and analysed within relatively short windows – ranging from daily to real-time



## Solution

- Hadoop Eco-System
- Spark Eco-System
- Solutions provided by cloud providers(AWS,GCP,Azure,Databricks etc)



## Hadoop Eco-System

![](https://static.packt-cdn.com/products/9781788995092/graphics/c8625da0-2ffb-41b7-bba8-58c33af68a30.png)

## Spark

![](https://static.packt-cdn.com/products/9781785888748/graphics/image_01_005.jpg)



## 3 Main Components

Both Hadoop and Spark Eco-Systems have 3 main components i.e. **Storage, Resource Management, Data Processing**.

- **Data Processing:** Both Hadoop and Spark engines are designed to process big data, and both frameworks have a lot of libraries to process both real-time and static data. See the following link for comparison [Battle: Apache Spark vs Hadoop MapReduce - TechVidvan](https://techvidvan.com/tutorials/apache-spark-vs-hadoop-mapreduce/)

- **Resource Management:** Resource Managers provides resources to all worker nodes as per need, it operates all nodes accordingly.

- We can say there are a master node and worker nodes available in a cluster. That master nodes provide an efficient working environment to worker nodes.

  Spark supports these cluster managers:

  1. Standalone cluster manager
  2. Hadoop Yarn
  3. Apache Mesos
  4. Kubernetes

  Hadoop supports these cluster managers:

  1. Hadoop Yarn
  2. Apache Mesos

  See the following link for comparison: [Apache Spark Cluster Manager: YARN, Mesos and Standalone - TechVidvan](https://techvidvan.com/tutorials/spark-cluster-manager-yarn-mesos-and-standalone/)

  
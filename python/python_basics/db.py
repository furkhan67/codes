import mysql.connector

import mysql.connector

mydb = mysql.connector.connect(
  host="localhost",
  port='3307',
  user="root",
  passwd="noman",
  database="first"
)

mycursor = mydb.cursor()

mycursor.execute("select id from t1")
#mydb.commit()

for x in mycursor:
    print("id:{}".format(x))


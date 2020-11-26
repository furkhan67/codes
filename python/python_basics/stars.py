a=int(input("Enter how many stars you want:"))
n=a
m=a
b=1
for i in range(0,m):
    for i in range(0,n):
        print("*", end ="")
    n-=2
    print("\n", end="")
    for i in range(0,b):
        print(" ", end="")
    b+=1
  
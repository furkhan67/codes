a=eval(input("enter 2 nos: "))
b=eval(input())
x=[]
c=1
for i in range(a,b+1):

    while(i!=1):
        if (i%2==1):
            i=(3*i)+1
        elif (i%2==0):
            i=i/2
        c+=1
    x.append(c)
    c=1
print("answer: "+str(max(x)))

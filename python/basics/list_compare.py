a=int(input("Enter length of string1: "))
b=int(input("Enter length of string2: "))
l1=[]
l2=[]
l3=[]

print("Enter string1 Elements: ")

for i in range(0,a):
    x=int(input(""))
    if x not in l1:
        l1.append(x)
    else:
        pass

print("Enter string2 Elements: ")

for i in range(0,b):
    x=int(input(""))
    if x not in l2:
        l2.append(x)
    else:
        pass



print("Common nos.: ")


def foo(l1,l2):
    
    for i in range(0,len(l1)):
        if l1[i] in l2:
            print(l1[i])
                

foo(l1,l2)
a=input("string pls: ")
c=[]

def foo(a):
    b=a.split()
    c.extend(b[::-1])
    s=''
    for i in c:
        s=s+' '+i

    return s

print(foo(a))
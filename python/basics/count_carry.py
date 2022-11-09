a = int(input("Enter Number #1: "))
b = int(input("Enter Number #2: "))
c = 0
if (a == 0 or b == 0):
    print("0")

else:

    while(a != 0 and b != 0):
        x = a % 10
        y = b % 10
        z = x+y
        if z > 9:
            c += 1
        a /= 10
        b /= 10

    print("No. of carries: ",c)

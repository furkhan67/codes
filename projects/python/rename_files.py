import os
f=[]
for f in  enumerate(os.listdir("./fonts")):
    for i in f:
        if type(i)!=int:
            src ='./fonts/'+ i 
            dst ='./fonts/'+ i[:1]+".png" 
            os.rename(src, dst)
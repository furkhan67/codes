from tkinter import *
import random
from PIL import Image,ImageTk



root=Tk()

"""def e_x(x):
    y=random.randrange(0,3)
    if x==1:
        b_1.configure(image=b)
    if x==2:
        b_2.configure(image=b)"""


a=Image.open("x.png")
b=ImageTk.PhotoImage(a)

b_1=Button()
b_1.grid(row=2,column=0)

b_2=Button()
b_2.grid(row=2,column=1)

b_3=Button()
b_3.grid(row=2,column=2)

b_4=Button()
b_4.grid(row=3,column=0)

b_5=Button()
b_5.grid(row=3,column=1)

b_6=Button()
b_6.grid(row=3,column=2)

b_7=Button()
b_7.grid(row=4,column=0)

b_8=Button()
b_8.grid(row=4,column=1)

b_9=Button()
b_9.grid(row=4,column=2)



root.mainloop()

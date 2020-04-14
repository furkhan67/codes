from tkinter import *

from PIL import Image,ImageTk

def iput():
    c=e_1.get()
    print(c)
    l_1.configure(text="mota",image=img_1,compound=CENTER)

root= Tk()

a=Image.open("mr.png")
#b= ImageTk(a)
img_1 = ImageTk.PhotoImage(a)
#b = Button(master, text="Click me", image=pattern, compound=CENTER)
b_1=Button(text='Enter',command=iput,compound=CENTER,border='0')
b_1.grid(row=0,column=2)

e_1=Entry()
e_1.grid(row=0,column=1)

l_1=Label(root,text="Enter Name")
l_1.grid(row=0,column=0)
root.title('lol')
#root.geometry("1280x720+0+5")
#root.resizable(1,1)
root.mainloop()

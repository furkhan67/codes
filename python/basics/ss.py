
from tkinter import *
import random
from PIL import Image,ImageTk
root=Tk()
root.title("Stone Paper Scissor")

#a = Image.open("1.jpg")
#b=ImageTk.PhotoImage(a)
t=StringVar()
t.set('Select Choice from above')
def a(x):

    y=random.randrange(0,3)

    if x==y:
        t.set('Draw')
        l_2.configure(bg='yellow')


    if (x==0 and y==1) or (x==1 and y==2) or (x==2 and y==0):
        t.set('You Lose')
        l_2.configure(bg='red')


    if (x==1 and y==0) or  (x==2 and y==1) or  (x==0 and y==2):
        t.set('You Win')
        l_2.configure(bg='green',)

    if y==0:
        l_1.configure(text='Comp: Stone',bg='grey')
    if y==1:
        l_1.configure(text='Comp: Paper',bg='white')
    if y==2:
        l_1.configure(text='Comp: Scissor',bg='maroon')

l_1=Label(text='Stone Paper Scissor',height=5,width=101,font=10)
l_1.grid(row=0,column=1,columnspan=3)

Stone=Button(text='Stone',command=lambda:a(0),bg='grey',fg='black',height=3,width=33,font=10)
Stone.grid(row=3,column=1)

paper=Button(text='Paper',command=lambda:a(1),height=3,width=33,bg='white',fg='black',font=10)
paper.grid(row=3,column=2)

scissor=Button(text='Scissor',command=lambda:a(2),height=3,width=33,bg='maroon',fg='black',font=10)
scissor.grid(row=3,column=3)

l_2=Label(textvariable=t,height=10,width=101,font=10)
l_2.grid(row=4,column=1,columnspan=3)



root.resizable(0,0)

root.mainloop()

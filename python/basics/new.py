from tkinter import *
import random
root=Tk()

t=StringVar()
t.set('Select Choice')
def a(x):

    y=random.randrange(0,3)

    if x==y:
        t.set('Draw')
        l_2.configure(bg='yellow')


    if x==0 and y==1 or x==1 and y==2 or x==2 and y==0:
        t.set('You Lose')
        l_2.configure(bg='red')


    if (x==1 and y==0) or  (x==2 and y==1) or  (x==0 and y==2):
        t.set('You Win')
        l_2.configure(bg='green')


    if y==0:
        l_1.configure(text='Comp: Stone')
    if y==1:
        l_1.configure(text='Comp: Paper')
    if y==2:
        l_1.configure(text='Comp: Scissor')

l_1=Label(text='Stone Paper Scissor',height=5,width=50)
l_1.grid(row=0,column=1,columnspan=3)

Stone=Button(text='Stone',command=lambda:a(0),bg='grey',fg='black',height=3,width=30)
Stone.grid(row=3,column=1)

paper=Button(text='Paper',command=lambda:a(1),height=3,width=30)
paper.grid(row=3,column=2)

scissor=Button(text='Scissor',command=lambda:a(2),height=3,width=30)
scissor.grid(row=3,column=3)

l_2=Label(textvariable=t,height=10,font=50)
l_2.grid(row=4,column=1,columnspan=3)





root.mainloop()

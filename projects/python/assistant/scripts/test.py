from tkinter import PhotoImage

from PIL import Image,ImageTk
import speech_recognition as sr
from gtts import gTTS
import os
import speak
import listen
import subprocess
from time import ctime
from tkinter import *
from tkinter import CENTER
import threading
import requests
from PIL import Image,ImageTk
import datetime
import webbrowser
from itertools import count
import play
from tkinter import font
import random
import gif

x=ctime()

day=x[0:3]
month=x[4:8]
date=x[8:10]
time=x[11:13]+" hours and "+x[14:16]+" minutes"
year=x[20:24]
root=Tk()
print(font.families())
#loading imagesimg_2= ImageTk.PhotoImage(b)

a=Image.open("files/mic1.png")
b=Image.open('files/thme14.png')
c=Image.open('files/black.png')
root.geometry("1920x1080+0+0")
img_2= ImageTk.PhotoImage(b)
img_1 = ImageTk.PhotoImage(a)
img_3 = ImageTk.PhotoImage(c)
#background image
background = Label(root,fg='red',compound=CENTER,bg='black')
background.place(x=0, y=0, relwidth=1, relheight=1)
background.grid(row=2,column=1)
background.pack(fill='both', expand=True)

#background animation
#l=gif.AnimatedGIF(background,"files/ezgif.com-gif-maker.gif")#
#l.pack()
TFont = font.Font(family='Terminal', size=30, weight='bold')
time=Label(background,text=month+"\n"+date,fg='turquoise',font=TFont,bg='black')
time.pack()

#Label gif


label = Label(background,bg='white',image=img_3)
label.pack()

#images

#l=Label(background,image=img_2,border=0)
#l.grid(row=,column=3)
#text widgets
TFont = font.Font(family='Helvetica', size=20, weight='bold')
T = Text(background, height=2, width=40,bg='white',fg='turquoise',wrap='word',font=TFont,border=0)
T.pack()
T.insert(END, "T")

T2 = Text(background, height=2, width=40,bg='white',fg='turquoise',wrap='word',font=TFont,border=0)
T2.pack()
T.insert(END, "T2")
#button
button_1 = Button(background, image=img_1 ,compound=CENTER,bg='white',fg='white',activebackground='black')
#button_1.bind("<Button-1>", example)
button_1.pack()
#background.image = img2

root.mainloop()

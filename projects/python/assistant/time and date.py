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
"""
root=Tk()
#loading images
a=Image.open("files/mic1.png")
b=Image.open('files/test-0.png')
c=Image.open('files/black.png')
#Creating Images
img_1 = ImageTk.PhotoImage(a)
img_2= ImageTk.PhotoImage(b)
img_3 = ImageTk.PhotoImage(c)"""
x=ctime()
day=x[0:3]
month=x[4:8]
date=x[8:10]
time=x[11:13]+" hours and "+x[14:16]+" minutes"
year=x[20:24]
"""#date Label
DFont = font.Font(family='Terminal', size=25, weight='bold')
dl=Label(root,text=month+"\n"+date,fg='turquoise',font=DFont,bg='black',image=img_2,compound=CENTER)
dl.pack()
framesd = [PhotoImage(file='files/c.gif',format = 'gif -index %i' %(i)) for i in range(30)]
def dupdate(ind):



            frame = framesd[ind]
            ind += 1
            if ind==30:
                dupdate(0)
            else:
                pass
            dl.configure(image=frame)
            root.after(80, dupdate, ind)



dupdate(0)
root.mainloop()
"""
time="11 hours and 58 minutes"
if int(time[:2])==12 and int(time[13:16])==00:
    print(time[:2]+" AM")
elif int(time[:2])==12 and int(time[13:16])>00:
    print(time[:2]+":"+time[13:16]+" PM")
elif int(time[:2])>12:
    print(str(int(time[:2])-12)+":"+time[13:16]+" PM")
else:
    print(time[:2]+":"+time[13:16]+" AM")

#print(time[0:2])
#print(time[13:16])
#print(time)

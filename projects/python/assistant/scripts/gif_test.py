from tkinter import *
import time
import os
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

root = Tk()

frames = [PhotoImage(file='files/test.gif',format = 'gif -index %i' %(i)) for i in range(3)]
#a=Image.open("files/mrb.png")
#img_1 = ImageTk.PhotoImage(a)

#label = Button(root,bg='black',image=img_1)
def update(ind):

    frame = frames[ind]
    ind += 1
    if ind==3:
        ind=0
    else:
        pass
    label.configure(image=frame)
    root.after(100, update, ind)

label = Label(root,bg='black')

label.pack()

update(0)
#root.after(0, update, 0)

root.mainloop()

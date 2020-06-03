import speech_recognition as sr
from gtts import gTTS
import os, subprocess, threading, requests, datetime, webbrowser, random, geocoder, time
from time import ctime
from itertools import count

from tkinter import *
from tkinter import CENTER
from PIL import Image,ImageTk
from tkinter import font

#import .scripts.speak, .scripts.play, .scripts.listen, .scripts.gif
from scripts.news import news
from scripts import speak, play, listen, gif
from scripts.translate import translate



#calculating time and date
x=ctime()
day=x[0:3]
month=x[4:8]
date=x[8:10]
ctime=x[11:13]+" hours and "+x[14:16]+" minutes"
year=x[20:24]
#GUI
#loading images
a=Image.open("files/test.gif")
b=Image.open('files/test-0.png')
c=Image.open('files/black.png')
d=Image.open('files/x.png')

#main window
root = Tk()
root.attributes("-fullscreen", True)
root.geometry("1920x1080+0+0")
root.title('Assistant')
root.resizable(1,1)
#root.iconify()
#Creating Images
img_1 = ImageTk.PhotoImage(a)
img_2= ImageTk.PhotoImage(b)
img_3 = ImageTk.PhotoImage(c)
img_4 = ImageTk.PhotoImage(d)


#root.configure(background='black')




#background image

background = Label(root,fg='red',compound=CENTER,bg='black',)
background.place(x=0, y=0, relwidth=1, relheight=1)
#background.grid(row=2,column=1)
#background.pack(fill='both', expand=True)
#background.image = img2

#x button
def min():
    root.attributes("-fullscreen", False)
    xb.destroy()
    #root.Iconify()
xb=Button(background,image=img_4,command=min,border=0,activebackground='black',bg="turquoise")
xb.grid(row=0,column=5,sticky="e")

#commands gif

cm_gif=gif.AnimatedGIF(background,"files/ezgif.com-crop.gif")
cm_gif.grid(row=2,column=5)


#resize empty rows,cols to insert other elements in centre

background.rowconfigure(0, weight=100)
#background.rowconfigure(2, weight=100)
background.rowconfigure(5, weight=100)
background.rowconfigure(7, weight=100)
background.columnconfigure(0, weight=100)
background.columnconfigure(2, weight=100)
background.columnconfigure(4, weight=100)

#Label gif

label=Label(background,image=img_3,border=0)
label.grid(row=2,column=3,sticky='s')
#lgif = gif.AnimatedGIF(background,"files/giffy.gif")

#date Label
DFont = font.Font(family='Terminal', size=20, weight='bold')
dl=Label(background,fg='turquoise',font=DFont,bg='black',image=img_2,compound=CENTER,text=month+"\n"+date)
#text=month+"\n"+date
dl.grid(row=2,column=0,sticky='n')
framesd = [PhotoImage(file='files/d2.gif',format = 'gif -index %i' %(i)) for i in range(30)]
def dupdate(ind):


    try:
            frame = framesd[ind]
            ind += 1

            dl.configure(image=frame)
            root.after(100, dupdate, ind)
    except IndexError:
            if ind==30:
                ind = 0
                root.after(100, dupdate, ind)
                ind+=1
            else:
                pass

dupdate(0)
#dl=Label(background,fg='turquoise',font=DFont,bg='black',image=img_2,compound=CENTER)
#dl.grid(row=2,column=1)

#text widgets

TFont = font.Font(family='Helvetica', size=12, weight='bold')
T = Text(background, height=2, width=40,bg='black',fg='turquoise',wrap='word',font=TFont,border=0)
T.grid(row=1,column=3)
T.insert(END, "I am your Assistant, press the MIC button to ask something")

T2 = Text(background, height=2, width=40,bg='black',fg='turquoise',wrap='word',font=TFont,border=0)
T2.grid(row=4,column=3)
#T2.insert(END, "I am your Assistant, press the MIC button to ask something")

#gif frames
frames = [PhotoImage(file='files/load.gif',format = 'gif -index %i' %(i)) for i in range(78)]

#running gifs



def update(ind):



            frame = frames[ind]
            ind += 1

            label.configure(image=frame)
            root.after(80, update, ind)





    #elif ind==25:
    #    root.after(80, update, ind)

    #elif ind>25:
    #    frames=NONE
    #    return 0





#stop gifs




#MAIN LOGIC
c=count(0)

def example():
    global start
    global elapsed
    def tmain():
        bupdate(0)
        start=time.clock()

        #x="yes"
        #update(0)
        if next(c) == 0 :
            T.replace("1.0", END, "Hello, what do u want me to do?\n")

            #lab.configure(text= "Hello, what do u want me to do?",fg='skyblue')
            speak.say("Hello, what do u want me to do?", 'en')
        else:
            speak.say("what can i do?",'en')
            #print(string)

        #while True:
        #t2.start()
        #global ind
        ind=0
        update(ind)
        #lgif.grid(row=2,column=1,sticky='s')


        string=listen.hear()

        if string==0:
            pass
        else:
            T2.replace("1.0",END,string+'\n')
        #speak.say("You said " + x,'en')

        if isinstance(string,int):
            string=''
            speak.say('Sorry i cant understand the audio, press mic button again to ask something.','en')
            T.replace( "1.0",END,"Sorry i cant understand the audio, press mic button again to ask something.\n")

            #stop()



            #x="no"
            #update(100)
            #lab.configure( text="Sorry i cant understand the audio, press mic button again to ask something.",fg='skyblue')

        elif string.strip() == 'hello' or string.strip() == 'hi' or string.strip() == 'hey':

            #T.insert(END,"Hello what can i do for you?\n")
            #print(a[r])

            #T.insert( END, res+'\n')
            #speak.say("Hello what can i do for you?", 'en')
            example()
            #update(100)
        elif string[:4] == 'open':
            string = string.replace('open', '').strip()

            try :
                T.insert(END,"Opening "+string+"\n")
                speak.say("Opening"+string,'en')
                subprocess.call(string)

            except FileNotFoundError:
                T.insert(END,'It seems that the path to the program is not set to the path, pls add it to path\n')



        elif string[:4]=='www.' :
        #string = string.replace('search', '').strip()

                T.insert(END,'Opening '+ string+'\n')
                webbrowser.open(string)

        elif string[:6] == 'search':
                string = string.replace('search', '').strip()

                T.insert(END,"searching for "+string+'\n')
                speak.say("Searching for"+string,'en')
                webbrowser.open('www.google.com/search?q=' + string)



        elif string [:9] == 'translate' and string [len(string)-5:len(string)] == 'Hindi':
                string = string.replace("translate", "")
                T.replace("1.0",END,"translating..." + string+'\n')
                speak.say('translating' + string, 'en')
                string=string.replace("in Hindi","").strip()
                T.replace("1.0",END,translate(string,'hi')+'\n')
                speak.say(translate(string,'hi'),'hi')

        elif string [:9] == 'translate' and string [len(string)-7:len(string)] == 'Spanish':
                string = string.replace("translate", "")
                T.replace("1.0",END,"translating..." + string+'\n')
                speak.say('translating' + string, 'en')
                string=string.replace("in Spanish","").strip()
                T.replace("1.0",END,translate(string,'es')+'\n')
                speak.say(translate(string,'es'),'es')


        elif string [:9] == 'translate' and string [len(string)-6:len(string)] == 'Arabic':
                string = string.replace("translate", "")
                T.replace("1.0",END,"translating..." + string+'\n')
                speak.say('translating' + string, 'en')
                string= string.replace("in Arabic","").strip()
                T.replace("1.0",END,translate(string,'ar')+'\n')
                speak.say(translate(string,'ar'),'ar')


        elif string [:9] == 'translate' and string [len(string)-6:len(string)] == 'French':
                string = string.replace("translate", "")
                T.replace("1.0",END,"translating..." + string+'\n')
                speak.say('translating' + string, 'en')
                string= string.replace("in French","").strip()
                T.replace("1.0",END,translate(string,'fr')+'\n')
                speak.say(translate(string,'fr'),'fr')



        elif string [:9] == 'translate' and string [len(string)-7:len(string)] == 'Marathi':
                string = string.replace("translate", "")
                T.replace("1.0",END,"translating..." + string+'\n')
                speak.say('translating' + string, 'en')
                string= string.replace("in Marathi","").strip()
                T.replace("1.0",END,translate(string,'mr')+'\n')
                speak.say(translate(string,'mr'),'mr')

        elif string[:11] == 'who are you':
                        speak.say("I am Friday", 'en')
        elif string[:12] == 'who made you':
                        speak.say("Allah made all the creatures", 'en')
        elif string [:15] == 'what time is it':
                        if int(ctime[:2])==12 and int(ctime[13:16])==00:
                            T.replace("1.0",END,"It's "+ctime[:2]+" AM")
                            speak.say("It's "+ctime[:2]+" AM", 'en')

                        elif int(ctime[:2])==12 and int(ctime[13:16])>00:

                            T.replace("1.0",END,"It's "+ctime[:2]+":"+ctime[13:16]+" PM")
                            speak.say("It's"+ctime[:2]+":"+ctime[13:16]+" PM", 'en')

                        elif int(ctime[:2])>12:
                            timeh=str(int(ctime[:2])-12)
                            #print(str(int(time[:2])-12)+":"+time[13:16]+" PM")
                            T.replace("1.0",END,"It's "+timeh+":"+ctime[13:16]+" PM")
                            speak.say("It's "+timeh+":"+ctime[13:16]+" PM", 'en')
                        else:
                            #print(time[:2]+":"+time[13:16]+" AM")
                            T.replace("1.0",END,"It's "+ctime[:2]+":"+ctime[13:16]+" AM")
                            speak.say("It's"+ctime[:2]+":"+ctime[13:16]+" AM", 'en')
                        #T.insert(END,"It's "+time)
                        #speak.say("It's"+time, 'en')
        elif string[:10] == 'I love you':
                        speak.say('Aww thats nice! can we get back to work now!', 'en')
        elif string [:16] == 'will you marry me':
                        speak.say('You should know you are not the only one who asked!', 'en')
        elif string[:8] == 'who am I':
                        speak.say('Legend', 'en')
        elif string[:18] == 'who is basirhat':
                        #print("Great Teacher!")
                        speak.say('Great teacher!', 'en')
        elif string[:28] == 'what is the capital of India':
                        speak.say('Dehli', 'en')
        elif string[:22] == 'name the worst college':
                        speak.say('Ask yourself', 'en')
        elif string[:11] == 'how are you':
                        speak.say('fine sir', 'en')
        elif string[:11] == 'best singer':
                        speak.say('zayn malik', 'en')
        elif string[:16] == 'show my location':
                        speak.say('Hold on, I will show you where you are', 'en')
                        g = geocoder.ip('me')
                        webbrowser.open("https://www.google.co.in/maps/place/Aurangabad,+Maharashtra+431001/@"+str(g.latlng[0])+","+str(g.latlng[1]))
        elif string[:23] == 'show me your source code':
                        speak.say('I am not supposed to show that', 'en')
        elif string [:14]== 'where do you live':
                        speak.say('In your system', 'en')

        elif string[:10] == 'rap for me':
                        #speak.say('Tu nanga hi toh aaya tha kya ghanta leke jayega', 'hi')
                        T.insert(END,"Tu nanga hee toh aaya tha kya ghanta leke jayega\n")
                        play.play('Time to travel.m4a')
        elif string [:19]== 'what is the weather':
                        speak.say('Wait a second', 'en')
                        webbrowser.open('https://www.bing.com/search?q=weather&qs=n&form=GEOTRI&sp=-1&pq=&sc=0-0&sk=&cvid=C3ED74D35EAD44928BD48D611A11389E')
                        subprocess.call("explorer.exe shell:appsFolder\Microsoft.BingWeather_8wekyb3d8bbwe!App")
                        #speak.say('Its sunny today', 'en')
        elif string [:11]== 'sing for me':
                        T.insert(END,"Hum honge kamiyab, hum honge kamiyab, hum honge kamiyab ek din. Man mein hai vishwas, poora hai vishwas, hum honge kamiyab ek din\n")
                        #speak.say('Hum honge kamiyab, hum honge kamiyab, hum honge kamiyab ek din. Man mein hai vishwas, poora hai vishwas, hum honge kamiyab ek din', 'hi')
                        #play.play('songname.mp3')

        elif string [:19]=="read news headlines":

            for i in news().findAll("a"):
                if i.text!="":
                    T.replace("1.0",END,i.text+"\n")
                    speak.say(i.text,'en')


        else :

                T.insert(END,"Here i have some results from google\n")
                speak.say("Here, i have some results from google",'en')
                webbrowser.open('www.google.com/search?q=' + string)

        next(c)
        next(c)
        elapsed=time.clock()-start
        #return elapsed

        #ind=26
        #lgif.stop_animation()

        #x="no"



    t1=threading.Thread(target=tmain)
    t1.start()
    #print(tmain)
    #time.sleep(10)

    #t2=threading.Thread(target=tg)
    #t2.start()




#button
button_1 = Button(background, image=img_1 ,border=0, command=example,bg='black',fg='white',activebackground='black')
button_1.grid(row=3,column=3)
framesb = [PhotoImage(file='files/test.gif',format = 'gif -index %i' %(i)) for i in range(3)]
def bupdate(ind):



            frame = framesb[ind]
            ind += 1

            button_1.configure(image=frame)
            root.after(110, bupdate, ind)


#button_1.pack()

#lab= Label(background, text='SHIT',fg='skyblue',bg='black')
#lab.grid(row=1,column=1)

root.mainloop()

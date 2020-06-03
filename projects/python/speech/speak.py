from gtts import gTTS
import pyglet
import time, os

i=0
def say(text, lang):
    global i
    file = gTTS(text = text, lang = lang, slow=False)
    filename = 'temp/'+str(i)+'.mp3'
    print(filename)
    file.save(filename)
    i=i+1

    from playsound import playsound
    playsound(filename)

    #time.sleep(music.duration)
    #os.remove(filename)

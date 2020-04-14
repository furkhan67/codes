from gtts import gTTS
import pyglet
import time, os

i=0
def say(text, lang):
    global i
    file = gTTS(text = text, lang = lang, slow=True)
    filename = 'temp/i.mp3'
    file.save(filename)
    i=i+1

    #music = pyglet.media.load(filename, streaming = False)
    #music.play()

    #time.sleep(music.duration)
    #os.remove(filename)

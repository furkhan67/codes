from gtts import gTTS
import time, os

def say(text, lang):
    file = gTTS(text = text, lang = lang)
    filename = 'temp/temp.mp3'
    file.save(filename)

    from playsound import playsound
    playsound(filename)
    os.remove(filename)

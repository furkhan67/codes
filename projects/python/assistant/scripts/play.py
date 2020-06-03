from gtts import gTTS
import time, os

def play(filen):
    filename = 'files/'+filen


    from playsound import playsound
    playsound(filename)

#say('a.mp3')
#play('Time to travel.m4a')

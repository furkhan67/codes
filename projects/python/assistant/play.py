from gtts import gTTS
import pyglet
import time, os

def play(filen):
    filename = 'files/'+filen


    music = pyglet.media.load(filename, streaming = False)
    music.play()

    time.sleep(music.duration)

#say('a.mp3')
#play('Time to travel.m4a')

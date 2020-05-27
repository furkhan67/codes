import pyttsx3
from pyttsx3 import voice
engine = pyttsx3.init('sapi5')
voices = engine.getProperty('voices')
engine.setProperty('voice', voices[1].id)
engine.say("dalindar imran")
engine.runAndWait() 
#print(voices)
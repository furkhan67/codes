import pyttsx3
from pyttsx3 import voice
engine = pyttsx3.init('sapi5')
voices = engine.getProperty('voices')
engine.setProperty('voice', voices[1].id)
engine.say("hello guys, welcome back to the channel! today we will do a 50,000 indian rupees PC build")
engine.runAndWait() 
print(voices)
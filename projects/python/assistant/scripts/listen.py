import speech_recognition as sr
from gtts import gTTS
r = sr.Recognizer()

def hear():
        with sr.Microphone() as source:
            audio = r.listen(source)
            try:
                text=r.recognize_google(audio)
                return text
            except Exception:
                return 0

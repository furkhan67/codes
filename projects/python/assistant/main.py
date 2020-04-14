import speech_recognition as sr
from gtts import gTTS
import os
import speak
import listen
import subprocess


while True:



	speak.say("Listening",'en')
	print("listening")
	string=listen.hear()

	#speak.say("You said " + x,'en')
	if isinstance(string,int):
		pass
	elif string[:9] == 'hello bot':
		print("Hello, What can i do for you?")
		speak.say("What can i do for you?"'en')
	elif string[:4] == 'open':
		string = string.replace('open', '').strip()

		try :
			print(f"Opening {string}")
			speak.say("Opening"+string,'en')
			subprocess.call(string)

		except FileNotFoundError:
			print('It seems that the path to the program is not set to the path, pls add it to path')

	elif string[:15]== 'search for www.':
		string=string.replace('search for','').strip()
		try:
			print('Opening '+string)
			webbrowser.open(string)
		except FileNotFoundError:
			print('It seems that the path to the program is not set to the path, pls add it to path')



	#elif string[:6] == 'search':
	#	string = string.replace('search', '').strip()
	#	try:
	#			print(f"searching for {string}")
	#			speak.say("Searching for"+string,'en')
	#			webbrowser.open('www.google.com/search?q=' + string

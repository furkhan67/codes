from speak import say
file = open('text.txt','r',encoding="utf8")
text="Hello there mate, how are you"
say(text,'en')
"""
for each in file:
    text+=each
    print(text)
    #text=eval(input("Enter urdu text:"))
    say(text,'ur')
"""
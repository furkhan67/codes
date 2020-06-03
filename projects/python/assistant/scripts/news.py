from scripts import speak
import requests
from bs4 import BeautifulSoup

def news():
    # the target we want to open
    url='https://www.hindustantimes.com/'

    #open with GET method
    resp=requests.get(url)

    #http_respone 200 means OK status
    if resp.status_code==200:
        #print("Successfully opened the web page")
        #print("The news are as follow :-\n")

        # we need a parser,Python built-in HTML parser is enough .
        soup=BeautifulSoup(resp.text,'html.parser')

        # l is the list which contains all the text i.e news
        #global list
        list=soup.find('div',{"class":"new-topnews-left"})

        #now we want to print only the text part of the anchor.
        #find all the elements of a, i.e anchor
        #print(l)
        #global ns
        #ns=[]

        #for i in l.findAll("a"):
        #    if i.text!="":
        #        ns.append(i.text)
                #print(i.text)
                #speak.say(i.text,'en')




    else:
        print("Error")
    return list

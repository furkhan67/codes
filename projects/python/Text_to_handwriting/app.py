import numpy as np
from PIL import Image
import gc
import os,glob

Image.MAX_IMAGE_PIXELS = 1221120000
import os
if not os.path.isdir('temp'):
    os.mkdir('temp') 

base ='fonts/set1/'
caps=base+"caps/"
cursive=base+"cursive/"
numbers=base+"numbers/"
symbols=base+"symbols/"
temp='temp/'
output='output/'
no_of_characters_per_line = 35 #set how many char to add per line
no_of_lines_per_page = 22       #set how many lines to add per page
#create lines
def make_line(list1,count):
 imgs    = [ Image.open(i) for i in list1 ]
 #min_shape = sorted( [(np.sum(i.size), i.size ) for i in imgs])[0][1]
 print("making line "+str(count))
 min_shape=(10*20,10*30)
 imgs_comb = np.hstack( (np.asarray( i.resize(min_shape) ) for i in imgs ) )
 imgs_comb = Image.fromarray( imgs_comb)
 imgs_comb.save( temp+str(count)+'.png' ) 
 del imgs_comb
 imgs=[]
 gc.collect()
 print("made") 

 #combine each line 
def make_page(img_list,filename):
 list1=img_list
 imgs    = [ Image.open(i) for i in list1 ]
 min_shape = sorted( [(np.sum(i.size), i.size ) for i in imgs])[0][1]
# min_shape=(300*y,200*y)
 imgs_comb = np.vstack( (np.asarray( i.resize(min_shape) ) for i in imgs ) )
 imgs_comb = Image.fromarray( imgs_comb)
 imgs_comb.save(filename)
 img = Image.open(filename)
 x, y = 2480,3508 #page size
 img = img.resize((x,y),Image.ANTIALIAS)
 img.save(filename,quality=55)
 
 

x=open("text.txt","r",encoding="utf8")
#print("ENTER TEXT TO CONVERT")
#x.write(input()+"\n")
list1=[]
listx=[i for i in x.read()]
#listx=listx[3:]


a=0
b=1
listc=[]
#adding \n after every "no_of_characters_per_line" letters
for i in range(0,len(listx)//no_of_characters_per_line):
  listc=listx[a*no_of_characters_per_line:b*no_of_characters_per_line]
  if "\n" in listc:
    pass
  else:
    listx.insert(b*no_of_characters_per_line,"\n")
  a+=1
  b+=1
  if (len(listx)-b*no_of_characters_per_line)==0:
    break


""" 
for i in range(0,len(listx)):
 if i%(no_of_characters_per_line)==0:
   #print(i)
   if(i==0) :
     pass
    
   else:
     listx.insert(i,"\n")

    
for i in range(0,10):
  print(str(i*50)+listx[i*50])

x=1
for i in range(0,len(listx)):
 if i%(no_of_characters_per_line)>0:
   if (len(listx)-i)>=22:
    if(listx[i]=='\n') :
      
      if(listx[x*no_of_characters_per_line])=="\n":

       del listx[x*no_of_characters_per_line]

      else:
        pass
 elif i%(no_of_characters_per_line)==0:
   if i==0:
     pass
   else:
     x+=1

   """   





#adding line break in the end of the listx
if '\n' in listx[-1]:
    pass
else:
    listx.append('\n')

listx.insert(0,"\n")
print(listx)

#print(listx)
y=0
count=0
for i in range(0,len(listx)):
 if len(list1)==0:
  list1.append(base+'space.png')

 
  
 if listx[i]=='\n':        #counting linebreaks and completing "no_of_characters_per_line" letters in each line 
  count=count+1
  list1.append(base+'space.png')
  """
  if len(list1)>no_of_characters_per_line:
  
    make_line(list1[:no_of_characters_per_line],count) #creating a line
    list2=list1[no_of_characters_per_line:]
    
    if(len(list2)<no_of_characters_per_line):
      for a in range(len(list2),no_of_characters_per_line,1):
        list2.append(base+'space.png')
    make_line(list2,count+1)
    count+=1
    
    #i-=len(list2)
    
    del list1,list2
    list1=[]
    list2=[]
    continue"""
  
  
  if(len(list1)<no_of_characters_per_line):
    for a in range(len(list1),50,1):
      list1.append(base+'space.png')
  make_line(list1,count) #creating a line
  del list1
  list1=[]
  continue

#checking the type of input and appending png in list1
 if(listx[i]>='a' and listx[i]<= 'z'):
   
   z=cursive+listx[i].lower()+".png"

 elif(listx[i]>='A' and listx[i]<='Z') :
   
   z=caps+listx[i].lower()+".png"

 elif(listx[i]>="0" and listx[i]<="9") :
   
   z=numbers+listx[i].lower()+".bmp"

 elif listx[i]==":":
   z=symbols+"colon.bmp"

 elif listx[i]=="/":
   z=symbols+"slash.bmp"
 
 elif listx[i]==";":
   z=symbols+"semicolon.bmp"
 
 elif listx[i]==".":
   z=symbols+"dot.bmp"

 elif listx[i]==",":
   z=symbols+"comma.bmp"

 elif listx[i]=='–':
   z=symbols+"dash.bmp"
 
 elif listx[i]=='-':
   z=symbols+"dash.bmp"
 
 elif listx[i]==')':
   z=symbols+"rbr.bmp" 
 
 elif listx[i]=='(':
   z=symbols+"rbl.bmp" 
 
 elif listx[i]=='"':
   z=symbols+"apostrophe.bmp" 

 elif listx[i]=="'":
   z=symbols+"singleapostrophe.bmp"
 
 elif listx[i]=="&":
   z=symbols+"and.bmp"
 
 elif listx[i]=="!":
   z=symbols+"exclamation.bmp"
 
 elif listx[i]=="?":
   z=symbols+"questionmark.bmp"  

 else: 
  z=base+'space.png'
 list1.append(z)
 y=y+1

#appending all lines in list1 
for i in range(1,count+1,1):
  z=temp+str(i)+".png"
  list1.append(z)


#merging all lines; 35 lines per page
list2=[]
i=0
c=1
while(len(list1)!=0):
  
  if i>=0 and i<no_of_lines_per_page and len(list1)!=0:
    list2.append(list1[i])
    i+=1
    #print("appending"+str(i))
    
    if len(list2)==no_of_lines_per_page:    #make page if all lines completed
      list2.insert(0,base+"line.png")
      list2.insert(len(list2),base+"line.png")
      make_page(list2,output+"final"+str(c)+".png")
      print("made page "+str(c))
      c+=1
      list2=[]
      continue

    elif len(list1)-len(list2)==0:    #make page after end of list1
      if len(list2)<no_of_lines_per_page:
        for i in range(len(list2),no_of_lines_per_page):
          list2.append(base+"line.png")
      list2.insert(0,base+"line.png")
      list2.insert(len(list2),base+"line.png")
      make_page(list2,output+"final"+str(c)+".png")
      print("made page "+str(c))
      list2.clear
      list1.clear
      filelist = [ f for f in os.listdir("temp/") if f.endswith(".png") ]
      for f in filelist:
          os.remove(os.path.join("temp/", f))
      print("done")
      break
  

  else:       #remove previous 35 lines from list1 after making each page and set i=0
    if len(list1)!=0:
      list1=list1[i:]
      i=0 





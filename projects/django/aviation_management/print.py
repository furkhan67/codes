
from Code2pdf.code2pdf import Code2pdf
import pdfkit
mypath="."
from os import listdir
from os.path import isfile, join
onlyfiles = [f for f in listdir(mypath) if isfile(join(mypath, f))]
print(onlyfiles)
options = {
    'dpi':196,
}
for f in onlyfiles:
    ifile,ofile,size,style = f, "prints/"+f+".pdf", "A4","vs"
    #pdfkit.from_file(ifile, ofile, options=options).fontSize(200)
    pdf = Code2pdf(ifile, ofile, size)  # create the Code2pdf object
    pdf.init_print()

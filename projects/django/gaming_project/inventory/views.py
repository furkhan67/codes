from django.shortcuts import render
from django.http import HttpResponseRedirect
from .models import CPU,Motherboard,RAM
from .forms import add_cpu_form,add_motherboard_form,add_ram_form

# Create your views here.

def add_cpu(request):
    form = add_cpu_form()
    
    if request.method == 'POST':
        form = add_cpu_form(request.POST)
        if form.is_valid:
            form.save()
        return HttpResponseRedirect('')
    return render (request,'inventory/add_cpu.html',{'form' : form})


def products_list(request):
    cpu_list = CPU.objects.all()
    Motherboard_list = Motherboard.objects.all()
    ram_list = RAM.objects.all()
    return render (request,'inventory/products_list.html',{'cpus': cpu_list, 'mobos' : Motherboard_list, 'rams' : ram_list })




from django.shortcuts import render,get_object_or_404
from .models import Course,Career
from django.contrib import messages
from django.http import HttpResponseRedirect
#from .forms import PostForm,ImageForm
from django.forms import modelformset_factory
from django.contrib.auth.decorators import login_required
def home_view(request):
    
    
    
    
    return render(request,'home.html',)


def course_list_view(request):
    course_list = Course.objects.all()
    
    
    return render(request,'courses.html',{'course_list':course_list})

def career_list_view(request):
    career_list = Career.objects.all()
    
    
    return render(request,'careers.html',{'course_list':career_list})


def career_detail_view(request, pk):

    career = Career.objects.get(id=pk)
    
    
    return render(request,'career-single.html',{"course": career})


def course_detail_view(request, pk):

    course = Course.objects.get(id=pk)
    
    
    return render(request,'course-single.html',{"course": course})
# Create your views here.

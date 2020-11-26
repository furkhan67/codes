from django.conf.urls import url,include
from django.contrib import admin
from . import views
from django.conf.urls.static import static
from django.conf import settings
from django.urls import path

urlpatterns = [
    url(r'^$', views.home_view , name="home"),
    url(r'^courses$', views.course_list_view , name="course_list"),
    url(r'^careers$', views.career_list_view , name="career_list"),
    path("career/<int:pk>/" , views.career_detail_view , name="career_detail"),
    path("course/<int:pk>/" , views.course_detail_view , name="course_detail"),
    
    


]
urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
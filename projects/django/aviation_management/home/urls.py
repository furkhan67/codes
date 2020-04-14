from django.conf.urls import url,include
from django.contrib import admin
from . import views
from django.conf.urls.static import static
from django.conf import settings
from django.urls import path

urlpatterns = [
    url(r'^$', views.home_view , name="home"),
    url(r'^courses$', views.course_list_view , name="course_list"),
    path("course/<int:pk>/" , views.course_detail_view , name="course_detail"),
    
    


]
urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
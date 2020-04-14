from django.urls import path
from django.urls import include
from .views import add_cpu,products_list


urlpatterns = [
path('products/',products_list ),
path('add_cpu/', add_cpu ),
]
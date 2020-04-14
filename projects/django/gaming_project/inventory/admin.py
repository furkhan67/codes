from django.contrib import admin
from .models import CPU,Motherboard,RAM

# Register your models here.



admin.site.register(CPU)
admin.site.register(Motherboard)
admin.site.register(RAM)
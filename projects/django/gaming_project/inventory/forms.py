from django.forms import ModelForm
from .models import CPU,Motherboard,RAM


class add_cpu_form(ModelForm):

    class Meta:
        model = CPU
        exclude=[]


class add_motherboard_form(ModelForm):

    class Meta:
        model = Motherboard
        exclude=[]


class add_ram_form(ModelForm):

    class Meta:
        model = RAM
        exclude=[]

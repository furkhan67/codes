from django.db import models

# Create your models here.
from django.db import models

# Create your models here.
from django.db import models
from django.contrib.auth.models import User
from django.utils import timezone
from django.urls import reverse
from django.template.defaultfilters import slugify




class Course(models.Model):
    
    Title =models.CharField(max_length=256)
    
    body =models.TextField(null=True,blank=True)

    image = models.ImageField(upload_to="images/")
    

    def __str__(self):
        return self.Title

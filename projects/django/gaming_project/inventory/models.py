from django.db import models

class CPU(models.Model):
    cpu_name = models.CharField(max_length=400)
    cpu_model_no = models.CharField(max_length=400)
    cpu_brand = models.CharField(max_length=400)
    cpu_tdp = models.IntegerField()
    cpu_gen =  models.IntegerField()
    cpu_unit_price = models.IntegerField()
    cpu_quantity = models.IntegerField()
    supported_chipset = models.CharField(max_length=100)

    def __str__(self):
        return self.cpu_name
    #mobo = models.ForeignKey(motherboard, on_delete = models.CASCADE)

class Motherboard(models.Model):
    mobo_name = models.CharField(max_length=400)
    mobo_model_no = models.CharField(max_length=400)
    chipset = models.CharField(max_length=400)
    mobo_brand = models.CharField(max_length=400)
    mobo_tdp = models.IntegerField()
    mobo_unit_price = models.IntegerField()
    mobo_quantity = models.IntegerField()
    mobo_size = models.CharField(max_length=400)
    supported_ram_type = models.CharField(max_length=400)
    

    def __str__(self):
        return self.mobo_name
    
class RAM(models.Model):
    ram_name = models.CharField(max_length=400)
    ram_model_no = models.CharField(max_length=400)
    ram_brand = models.CharField(max_length=400)
    ram_tdp = models.IntegerField()
    
    ram_unit_price = models.IntegerField()
    ram_quantity = models.IntegerField()

    def __str__(self):
        return self.ram_name
    #supported_chipset = models.CharField(max_length=100,choices=)
    #mobo = models.ForeignKey(motherboard, on_delete = models.CASCADE)


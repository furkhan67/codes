# Generated by Django 2.1.2 on 2019-02-16 14:12

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('app', '0011_remove_vendor_status'),
    ]

    operations = [
        migrations.AddField(
            model_name='vendor',
            name='status',
            field=models.CharField(default=False, max_length=20),
        ),
    ]

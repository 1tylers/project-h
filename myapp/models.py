from django.db import models

class Parts(models.Model):
    number = models.IntegerField(primary_key=True)
    description = models.CharField(max_length=50)
    price = models.DecimalField(max_digits=8, decimal_places=2)
    weight = models.DecimalField(max_digits=4, decimal_places=2)
    pictureURL = models.CharField(max_length=50)

    class Meta:
        managed = False
        db_table = 'parts'

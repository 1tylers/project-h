from django.shortcuts import render
from .models import Parts

def parts_list(request):
    parts = Parts.objects.all()
    return render(request, 'parts_list.html', {'parts': parts})
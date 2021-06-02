from django.contrib import admin
from .models import Item, Content, Device, Visitor

admin.site.register(Item)
admin.site.register(Content)
admin.site.register(Device)
admin.site.register(Visitor)
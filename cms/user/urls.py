
from django.contrib import admin
from django.urls import include, path
from rest_framework import routers
from .views import UserViewSet, GroupViewSet


router = routers.DefaultRouter()
router.register(r'v1/user', UserViewSet)
router.register(r'v1/userGroup', GroupViewSet)
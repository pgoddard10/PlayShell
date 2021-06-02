
from django.contrib import admin
from django.urls import include, path
from rest_framework import routers
from .views import ItemViewSet, ContentViewSet, DeviceViewSet, VisitorViewSet, LimitedVisitorViewSet

"""
# uncomment the below for nested urls
# e.g. /v1/item/1/content/2
from rest_framework_extensions.routers import NestedRouterMixin
class NestedDefaultRouter(NestedRouterMixin, routers.DefaultRouter):
    pass

router = NestedDefaultRouter()
item_router = router.register('v1/item', ItemViewSet)
item_router.register(
    'content', ContentViewSet,
    basename='item_content',
    parents_query_lookups=['item'])

"""
router = routers.DefaultRouter()
router.register(r'v1/item', ItemViewSet)
router.register(r'v1/content', ContentViewSet)
router.register(r'v1/device', DeviceViewSet)
router.register(r'v1/visitor', VisitorViewSet)
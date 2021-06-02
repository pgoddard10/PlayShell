from rest_framework import serializers
from heritage import models

class ContentSerializer(serializers.ModelSerializer):
    class Meta:
        model = models.Content
        fields = '__all__'

class ItemSerializer(serializers.ModelSerializer):
    item_content = ContentSerializer(required=False, many=True)
    class Meta:
        model = models.Item
        fields = '__all__'

class DeviceSerializer(serializers.ModelSerializer):
    class Meta:
        model = models.Device
        fields = '__all__'

class VisitorSerializer(serializers.ModelSerializer):
    class Meta:
        model = models.Visitor
        fields = '__all__'

class LimitedVisitorSerializer(serializers.ModelSerializer):
    class Meta:
        model = models.Visitor
        fields = ('id',)
from rest_framework import viewsets, permissions, filters
from heritage.serializers import ItemSerializer, ContentSerializer, DeviceSerializer, VisitorSerializer, LimitedVisitorSerializer
from heritage import models
from rest_framework.exceptions import ValidationError
from django_filters.rest_framework import DjangoFilterBackend
import os

from rest_framework_extensions.mixins import NestedViewSetMixin #for nested URLs

def getSoundFileName(contentId):
    if isinstance(contentId, int):
        contentId = str(contentId)
    return "content_" + contentId + ".mp3"

def validateSoundOptions(contentId, useTts, text, soundFile):#self, serializer_class):
    from django.conf import settings
    folder = settings.MEDIA_ROOT
    filename = getSoundFileName(contentId)

    if useTts is True and len(text) == 0:
        raise ValidationError('If using TTS, you must specify some text to convert to speech.')
    elif soundFile is None:
        if os.path.isfile(folder + filename) is False: #check to see if a file already exists for this content
            raise ValidationError('If not using TTS, you must upload a valid sound/audio file')
    return True

class ItemViewSet(NestedViewSetMixin, viewsets.ModelViewSet):
    """
    API endpoint that allows items to be viewed or edited.
    """
    queryset = models.Item.objects.all().order_by('-name')
    serializer_class = ItemSerializer
    permission_classes = [permissions.IsAuthenticated]
    filter_backends = [filters.SearchFilter,DjangoFilterBackend]
    filterset_fields = ['name','heritageId','location','isActive']
    search_fields = ['name','heritageId','location']

class ContentViewSet(NestedViewSetMixin, viewsets.ModelViewSet):
    """
    API endpoint that allows content to be viewed or edited.
    """
    queryset = models.Content.objects.all().order_by('-name')
    serializer_class = ContentSerializer
    permission_classes = [permissions.IsAuthenticated]
    filter_backends = [filters.SearchFilter,DjangoFilterBackend]
    filterset_fields = ['name','nfcTag','useTts','gesture','item']
    search_fields = ['name','text']

    def perform_update(self, serializer_class):
        contentId = self.get_object().getId()
        useTts = serializer_class.validated_data['useTts']
        text = serializer_class.validated_data['text']
        soundFile = serializer_class.validated_data['soundFile']
        
        if(validateSoundOptions(contentId,useTts,text,soundFile)):
            serializer_class.save()

    from rest_framework.decorators import action
    @action(methods=['get'], detail=True)
    def scanNfcTag(self, request, *args, **kwargs):
        """
        updates 'heritage/scan_tag_app/json_exchange/content.json' with the content id
        The back-end system will now keep looking for an NFC tag to be scanned. Once scanned, it will link the content id with the NFC tag id
        """
        content = self.get_object()
        
        from django.conf import settings
        jsonFile = os.path.join(settings.BASE_DIR, "heritage/scan_tag_app/json_exchange/content.json")

        import json
        data = {
            'id': content.id,
            'name': content.name,
            'item': content.item.id
        }

        with open(jsonFile, 'w') as outfile:
            json.dump(data, outfile)

        #target_content = int(kwargs['target_id'])
        content = {'success': 'successfully triggered a prompt to wait for NFC tag scanning...'}
        from rest_framework import status
        from rest_framework.response import Response
        return Response(content, status=status.HTTP_202_ACCEPTED)
    
    @action(methods=['get'], detail=True)
    def getNfcTag(self, request, *args, **kwargs):
        """
        updates 'heritage/scan_tag_app/json_exchange/content.json' with the content id
        The back-end system will now keep looking for an NFC tag to be scanned. Once scanned, it will link the content id with the NFC tag id
        """
        content = self.get_object()
        
        from django.conf import settings
        import json
        jsonFile = os.path.join(settings.BASE_DIR, "heritage/scan_tag_app/json_exchange/nfcData.json")

        with open(jsonFile) as f:
            d = json.load(f)

        print("content.json opened and content ID obtained: ", d['id'])
        print("content.json opened and NFC ID obtained: ", d['nfcTag'])

        serializer_class = ContentSerializer(data=d)
        serializer_class.is_valid(self)
        serializer_class.save()
    
        #target_content = int(kwargs['target_id'])
        content = {'id': d['id'], 'nfcTag': d['nfcTag']}
        from rest_framework import status
        from rest_framework.response import Response
        return Response(content, status=status.HTTP_202_ACCEPTED)

class DeviceViewSet(viewsets.ModelViewSet):
    """
    API endpoint that allows devices to be viewed or edited.
    """
    queryset = models.Device.objects.all().order_by('-networkName')
    serializer_class = DeviceSerializer
    permission_classes = [permissions.IsAuthenticated]
    filter_backends = [filters.SearchFilter,DjangoFilterBackend]
    filterset_fields = ['networkName']
    search_fields = ['networkName', 'notes']

class VisitorViewSet(viewsets.ModelViewSet):
    """
    API endpoint that allows visitors to be viewed or edited.
    """
    queryset = models.Visitor.objects.all().order_by('-lastName')
    serializer_class = VisitorSerializer
    permission_classes = [permissions.IsAuthenticated]
    filter_backends = [filters.SearchFilter,DjangoFilterBackend]
    filterset_fields = ['firstName', 'lastName','email','phoneNumber','address1','address2','address3','address4','postcode']
    search_fields = ['firstName', 'lastName','email']

class LimitedVisitorViewSet(viewsets.ModelViewSet):
    """
    API endpoint that allows visitors to be viewed or edited.
    """
    queryset = models.Visitor.objects.all().order_by('-lastName')
    serializer_class = LimitedVisitorSerializer
    permission_classes = [permissions.IsAuthenticated]
    
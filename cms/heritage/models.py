from django.db import models
import datetime
from django.utils import timezone
from django.core.validators import FileExtensionValidator
from django.db.models.signals import pre_save
from django.dispatch import receiver
import pyttsx3        
engine = pyttsx3.init()

class Item(models.Model):
    name = models.CharField('Name/Description', max_length=255)
    heritageId = models.CharField('Alternative ID',
                            help_text="Useful to cross-reference with other systems",
                            max_length=200, null=True, blank=True, unique=True)
    website = models.URLField('Website',
                            help_text="A page on your website that is relevant to this item",
                            max_length=255, null=True, blank=True)
    location = models.CharField(help_text="Where is your item located on the premises",
                            max_length=200, null=True, blank=True)
    createdDate = models.DateTimeField('Created Date', auto_now_add=True)
    lastModifiedDate = models.DateTimeField('Last Modified Date', auto_now=True)
    isActive = models.BooleanField('Active?',
                            help_text="Is this item available for playback on the devices?",
                            default=True)

    def __str__(self):
        return self.name

    def getId(self):
        return self.id
        
    def was_created_recently(self):
        now = timezone.now()
        return now - datetime.timedelta(days=5) <= self.createdDate <= now

    def was_modified_recently(self):
        now = timezone.now()
        return now - datetime.timedelta(days=5) <= self.lastModifiedDate <= now


class Content(models.Model):    
    NON         = 'NON'
    SHAKE       = 'SHK'
    TILT        = 'TLT'
    TURNOVER    = 'TRN'

    GESTURE_TYPES = (
        (NON, 'none'),
        (SHAKE, 'shake'),
        (TILT, 'tilt'),
        (TURNOVER, 'turn over/upside down')
    )

    item = models.ForeignKey(Item, on_delete=models.CASCADE,related_name='item_content',)
    name = models.CharField('Name/Description', max_length=255)
    nfcTag = models.CharField('NFC Tag', max_length=50, null=True, blank=True)
    useTts = models.BooleanField('Use TTS',
                            help_text="Do you the system automatically generate an audio file from the text you provide?",
                            default=True)
    text = models.TextField('Text to convert to speech',
                            help_text="Text to convert into an audio file",
                            null=True, blank=True)
    soundFile = models.FileField(help_text="Audio file for use instead of text to be converted to sound",
                                validators=[FileExtensionValidator(allowed_extensions=['mp3'])],
                                null=True, blank=True)#, upload_to=fileStorageLocation)
    gesture = models.CharField(help_text="If set, the gesture will be prompted for after the audio has played",
                            max_length=25, choices=GESTURE_TYPES, default=NON)
    createdDate = models.DateTimeField('Created Date', auto_now_add=True)
    lastModifiedDate = models.DateTimeField('Last Modified', auto_now=True)

    def __str__(self):
        return self.name

    def getId(self):
        return self.id

    def getItemId(self):
        return self.item.getId()
        
    def was_created_recently(self):
        now = timezone.now()
        return now - datetime.timedelta(days=5) <= self.createdDate <= now

    def was_modified_recently(self):
        now = timezone.now()
        return now - datetime.timedelta(days=5) <= self.lastModifiedDate <= now

def convertTextToSpeech(text,filename):
    from django.conf import settings
    folder = settings.MEDIA_ROOT
    engine.save_to_file(text, folder + filename)
    engine.runAndWait()

def generateRandString():
    import random, string
    randomString = ''.join(random.SystemRandom().choice(string.ascii_uppercase + string.digits) for _ in range(6))
    return str(randomString)

# method for updating
@receiver(pre_save, sender=Content)
def saveSoundFile(sender, instance, **kwargs):
    from django.conf import settings
    from django.core.files import File, uploadedfile
    import os
    folderPath = settings.MEDIA_ROOT
    if not os.path.exists(folderPath):
        os.makedirs(folderPath)

    randomString = generateRandString()
    randomString = "41EF19"

    if instance.useTts is True:
        filename = randomString + "_content.mp3"
        while os.path.isfile(os.path.join(folderPath, filename)):
            randomString = generateRandString()
            filename = randomString + "_content.mp3"
        convertTextToSpeech(instance.text,filename)
        uploadedfile.TemporaryUploadedFile = filename
        instance.soundFile.file = uploadedfile.TemporaryUploadedFile
        instance.soundFile.name = filename
        import time
        time.sleep(10)
    
    elif(instance.soundFile.file is not None):
        filename = randomString + "_" + instance.soundFile.name
        while os.path.isfile(os.path.join(folderPath, filename)):
            randomString = generateRandString()
            filename = randomString + "_" + instance.soundFile.name
        instance.soundFile.name = filename

@receiver(models.signals.post_delete, sender=Content)
def delete_file(sender, instance, *args, **kwargs):
    """ Deletes sound files on `post_delete` """
    if instance.soundFile:
        """ Deletes file from filesystem. """
        import os
        if os.path.isfile(instance.soundFile.path):
            os.remove(instance.soundFile.path)


class Device(models.Model):
    """
    Details about the audio tour guide devices
    """
    networkName = models.CharField('Network Name/IP Address',max_length=40, unique=True)
    notes = models.TextField('Any notes about this particular device', null=True, blank=True)

    def __str__(self):
        return self.networkName

class Visitor(models.Model):
    firstName = models.CharField(max_length=30)
    lastName = models.CharField(max_length=30)
    email = models.EmailField(max_length=200, unique=True, null=True, blank=True)
    sendNotifications = models.BooleanField('Send Email Notifications?',default=False)
    phoneNumber = models.CharField(max_length=15, null=True, blank=True)
    address1 = models.CharField(max_length=30, null=True, blank=True)
    address2 = models.CharField(max_length=30, null=True, blank=True)
    address3 = models.CharField(max_length=30, null=True, blank=True)
    address4 = models.CharField(max_length=30, null=True, blank=True)
    postcode = models.CharField(max_length=15, null=True, blank=True)

    def __str__(self):
        return self.firstName + " " + self.lastName

@receiver(pre_save, sender=Visitor)
def emailClean(sender, instance, *args, **kwargs):
    """
    If no email is specified, set to null instead of an empty string.
    It does this to avoid validation errors on unique emails, i.e.
    multiple users can have no email address
    """
    if instance.email is '':
        instance.email = None
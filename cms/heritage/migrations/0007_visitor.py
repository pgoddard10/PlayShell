# Generated by Django 3.1.7 on 2021-05-28 10:56

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('heritage', '0006_device'),
    ]

    operations = [
        migrations.CreateModel(
            name='Visitor',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('firstName', models.CharField(max_length=30)),
                ('lastName', models.CharField(max_length=30)),
                ('email', models.EmailField(blank=True, max_length=200, null=True, unique=True)),
                ('sendNotifications', models.BooleanField(default=False, verbose_name='Send Email Notifications?')),
                ('phoneNumber', models.CharField(blank=True, max_length=15, null=True)),
                ('address1', models.CharField(blank=True, max_length=30, null=True)),
                ('address2', models.CharField(blank=True, max_length=30, null=True)),
                ('address3', models.CharField(blank=True, max_length=30, null=True)),
                ('address4', models.CharField(blank=True, max_length=30, null=True)),
                ('postcode', models.CharField(blank=True, max_length=15, null=True)),
            ],
        ),
    ]


# PlayShell: A Low-cost, Fun Audio Experience for Heritage Centres
## What is PlayShell?
Various barriers prevent blind and visually impaired people accessing the rich multisensory experiences available at heritage centres. These barriers include large bodies of text and items in glass cases, which are difficult to see. Feedback from the blind community reflects poorly upon the inflexibility of guided tours. Technology-based accessibility tools are often laden with visually heavy interfaces or require storage space or power at each exhibit.

We present a low-cost digital audio guide that can be combined with existing 2D and 3D systems, as well as 3D printed reliefs and replicas. The technology aims to work in a variety of environments, allowing curators to retrofit it into centres with limited space. The handheld system provides pre-recorded audio to visitors as they explore the centre. Sound is triggered via ’tap’ onto Near-Field Communication (NFC) tags, which are placed by the curator or artist. Content is updated via a central system, which replicates to each device. A storytelling process can be created through the addition of motion gestures (e.g. shake), enhancing the experience for all visitors.

Our research is published online: [https://doi.org/10.1145/3411109.3411132](https://doi.org/10.1145/3411109.3411132)
## Architecture
### Hardware
There are two types of systems used for this overall solution: A tour guide device for visitors to carry around the centre and a central server to manage the content.

#### Visitor's Audio Guide
This produces one device for a visitor to take around the centre. Multiple visitor devices will need creating to allow multiple visitors to use the solution at any given time.
 - Raspberry Pi Zero W
 - RFID RC522 reader
 - MPU6050 accelerometer
 - PiSugar LiPo battery
 - a [custom-made one-transistor mono amplifier soundboard](https://www.instructables.com/id/One-Transistor-Audio-for-Pi-Zero-W) (in the absence of a built-in 3.5mm audio jack on the Pi) or use Bluetooth headphones.

Turn off the device (to ensure no power shorting) and set up the hardware on the following pins. The soundboard taps into the HDMI output and connected to the following pins:

 - Power to Pin 4 (5v Power)
 - Ground to Pin 39 (Ground)
 - Data to Pin 33 (GPIO 13)

The accelerometer (MPU5060) uses the i2c interface on these pins:

 - SDA connects to Pin 3 (GPIO 2) on the Pi
 - SCL connects to Pin 5 (GPIO 3) on the Pi
 - GND connects to Pin 6 (Ground) on the Pi
 - VIN connects to Pin 1 (3.3v Power) on the Pi

The RC522 NFC Reader uses the SPI interface, connecting to the Raspberry Pi on the following pins:

 - SDA connects to Pin 24 (GPIO 8) on the Pi
 - SCK connects to Pin 23 (GPIO 11) on the Pi
 - MOSI connects to Pin 19 (GPIO 10) on the Pi
 - MISO connects to Pin 21 (GPIO 9) on the Pi
 - GND connects to Pin 20 (Ground) on the Pi
 - RST connects to Pin 22 (GPIO 25) on the Pi
 - 3.3v connects to Pin 17 (3.3v Power) on the Pi

#### Central Management System / Server
Only one server is needed per centre.
 - Raspberry Pi 4 (Model B)
 - RFID RC522 reader
 
Turn off the device (to ensure no power shorting) and set up the NFC reader on the following pins. The RC522 NFC Reader uses the SPI interface, connecting to the Raspberry Pi on the following pins:
 - SDA connects to Pin 24 (GPIO 8) on the Pi
 - SCK connects to Pin 23 (GPIO 11) on the Pi
 - MOSI connects to Pin 19 (GPIO 10) on the Pi
 - MISO connects to Pin 21 (GPIO 9) on the Pi
 - GND connects to Pin 20 (Ground) on the Pi
 - RST connects to Pin 22 (GPIO 25) on the Pi
 - 3.3v connects to Pin 17 (3.3v Power) on the Pi

### Software
#### Installation
The installation is a bit cumbersome at the moment. Future plans include scripting the prerequisites.

##### Visitor's Audio Guide
Boot up the Raspberry Pi. Open the Terminal and run the commands outlined below:

SQLite3 is installed with the following command:

    $ sudo apt-get install libsqlite3-dev -y

To install the JSON CPP library for the tag scanning app:

    $ sudo apt-get install libjsoncpp-dev -y

Enable SPI interfacing (for the NFC reader) and sound over HDMI (for the soundboard):

    $ sudo nano /boot/config.txt

add these lines at the bottom:

    dtparam=spi=on
    dtoverlay=pwm-2chan,pin=18,func=2,pin2=13,func2=4

<< Exit and save the changes. >>

Open the Raspberry Pi configuration

    $ sudo raspi-config

<< Go to ADVANCED -> AUDIO -> FORCE 3.5mm headphone jack >>

<< Go to Interfacing Options -> i2c -> Yes >>

SFML is an easy-to-install C library that can process the sound created by the .wav files. It can be installed with the following line:

    $ sudo apt-get install libsfml-dev -y

Enable SSH (for the SFTP file transfer):

    $ sudo systemctl enable ssh
    $ sudo systemctl start ssh

Enable i2c (for the accelerometer)

    $ sudo apt-get install libi2c-dev -y

Now download the latest copy of the project files

<< Browse to your chosen install directory >>

    $ git clone https://github.com/pgoddard10/PlayShell.git

Restart to ensure all changes take hold

    $ sudo reboot

Make the program:

<< cd into the PlayShell/app directory >>

    $ make clean
    $ sudo make

##### Central Management System / Server

Boot up the Raspberry Pi. Open the Terminal and run the commands outlined below:

    $ cd ~/Downloads

Apache, Composer & PHP

    $ sudo apt install apache2 -y
    $ sudo apt install curl php-cli php-mbstring git unzip -y
    $ curl -sS https://getcomposer.org/installer -o composer-setup.php
    $ sudo apt install php libapache2-mod-php -y
    $ sudo apt-get install -y php-ssh2

Copy the latest hash from https://composer.github.io/pubkeys.html and substitute the ‘xxxx’ in the following line with the one copied:

    $ HASH=xxxx
    $ php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
    $ sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

TTS

    $ wget http://ftp.us.debian.org/debian/pool/non-free/s/svox/libttspico0_1.0+git20130326-9_armhf.deb
    $ wget http://ftp.us.debian.org/debian/pool/non-free/s/svox/libttspico-utils_1.0+git20130326-9_armhf.deb
    $ sudo apt-get install -f ./libttspico0_1.0+git20130326-9_armhf.deb ./libttspico-utils_1.0+git20130326-9_armhf.deb

Database – SQLite3

    $ sudo apt-get install php7.3-sqlite

To enable SQLite3 interaction with PHP, the php.ini file needs editing:

    $ sudo nano /etc/php/7.3/apache2/php.ini

Add following line in the Dynamic Extensions section:

    extension=sqlite3

To install the JSON CPP library for the tag scanning app:

    $ sudo apt-get install libjsoncpp-dev -y

Enable SPI interfacing (for the NFC reader):

    $ sudo nano /boot/config.txt

add this line at the bottom:

    dtparam=spi=on

Enable SSH (for the SFTP file transfer):

    $ sudo systemctl enable ssh
    $ sudo systemctl start ssh

Enable .htaccess overrides (used for updating the file size maximum)

    $ sudo nano /etc/apache2/apache2.conf

Locate this section:

    <Directory /var/www/>
	    Options Indexes FollowSymLinks
	    AllowOverride None
	    Require all granted
    </Directory>

Change to

    <Directory /var/www/>
	    Options Indexes FollowSymLinks
	    AllowOverride None
	    Require all granted
    </Directory>

Set the required file permissions for the program’s operation:

    $ cd /var/www/html
    $ git clone https://github.com/pgoddard10/PlayShell.git
    $ cd PlayShell
    $ sudo chown www-data cms
    $ sudo chmod 775 cms
    $ sudo chown www-data cms/*
    $ sudo chown www-data cms/audio_culture.db
    $ sudo chmod 775 cms/audio_culture.db
    $ sudo chown www-data cms/json/tag_setup/*.json
    $ sudo chmod 777 cms/json/tag_setup/*.json
    $ sudo chown www-data cms/json/device_data_exchange/*

Compile the app that is responsible for the scanning of tags through the web interface

    $ cd scan_tag_app
    $ sudo make

Finally, reboot to ensure the changes are loaded

    $ sudo reboot

#### Running the software
##### Audio Device
<< Browse to your chosen install directory >>

    $ ./main.run
##### Central Management System / Server
    $ cd /var/www/html/PlayShell/cms/scan_tag_app
    $ ./main.run

The web interface will run on it's own.

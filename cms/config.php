<?php
/**
 * For use throughout the Centralised Management System
 * Contains the system variables
 *
 * @author Paul Goddard, paul2.goddard@live.uwe.ac.uk
 * @date Spring 2020 
 * 
 */

//system settings
define("DATABASE_FILE","audio_culture.db");
define("SITE_NAME","Audio Culture Admin");

//display PHP errors (i.e. PHP debugging = on)
if (!ini_get('display_errors')) {
    ini_set('display_errors', '1');
}

//staff roles for access to parts of the CMS
define("STAFF_DB_MANAGER",1);
define("CONTENT_MANAGER",2);
define("REPORT_MANAGER",3);
define("VISITOR_DB_MANAGER",4);
define("DEVICE_MANAGER",5);
define("TBC",6);

define("CONTENT_ID_FILE","json/tag_setup/content.json");//contains the outgoing content id (i.e. from the PHP script to the C++ app)
define("NFC_ID_FILE","json/tag_setup/tag_data.json");//contains the returning NFC tag id (i.e. from the C++ app to the PHP page)

define("PUBLISHED_CONTENT_FOLDER","json/device_data_exchange/"); //location of the data to be copied onto individual devices
define("PUBLISHED_CONTENT_FILE","published_content.json"); //content info from the database to be copied onto the device

define("AUDIO_FOLDER","audio/"); //folder to store the sound files

define("VISITOR_DEVICE_PREFIX","ac-device"); //numbers will be added to the name, i.e. 'ac-device'   becomes:  ac-device-1     ac-device-2    ac-device-3    etc
define("NUMBER_OF_VISITOR_DEVICES",1); //actually only have 1 for testing, but this will help nicely simulate other devices being out-of-range
define("FTP_USERNAME","pi"); define("FTP_PASSWORD","raspberry"); //used for transferring visitor data from the CMS to individual devices.
define("DEVICE_DATA_FOLDER","/home/pi/audio_culture/app/cms_data_exchange/"); //folder to transfer the files to on the individual device

//the current status of individual visitor devices
define("DEVICE_READY",0);
define("DEVICE_IN_USE",1);
define("DEVICE_UPDATING",2);
define("CMS_UPDATING",3);
?>
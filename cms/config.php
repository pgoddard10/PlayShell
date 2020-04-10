<?php
/**
 * For use throughout the Centralised Management System
 * Contains the system variables
 *
 * @author Paul Goddard, paul2.goddard@live.uwe.ac.uk
 * @date Spring 2020 
 * 
 */

define("DATABASE_FILE","audio_culture.db");

//staff roles for access to parts of the CMS
define("STAFF_DB_MANAGER",1);
define("CONTENT_MANAGER",2);
define("REPORT_MANAGER",3);
define("VISITOR_DB_MANAGER",4);
define("DEVICE_MANAGER",5);

define("CONTENT_ID_FILE","json/tag_setup/content.json");//contains the outgoing content id (i.e. from the PHP script to the C++ app)
define("NFC_ID_FILE","json/tag_setup/tag_data.json");//contains the returning NFC tag id (i.e. from the C++ app to the PHP page)

define("PUBLISHED_CONTENT_FOLDER","json/device_data_exchange/"); 
define("PUBLISHED_CONTENT_FILE","published_content.json"); 

define("AUDIO_FOLDER","audio/"); //folder to store the sound files

define("VISITOR_DEVICE_PREFIX","ac-device"); //numbers will be added to the name, i.e. 'ac-device'   becomes:  ac-device-1     ac-device-2    ac-device-3    etc
define("NUMBER_OF_VISITOR_DEVICES",10); //actually only have 1 for testing, but this will help nicely simulate 2 devices being out-of-range
define("FTP_USERNAME","pi"); define("FTP_PASSWORD","raspberry"); //used for transferring visitor data from the CMS to individual devices.
define("DEVICE_DATA_FOLDER","/home/pi/audio_culture/app/json/cms_data_exchange/")

?>
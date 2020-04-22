#ifndef Content_Controller_h
#define Content_Controller_h

#include <vector>
#include <string.h>
#include "RC522.h" //for NFC scanning
#include <sqlite3.h> //for the database interaction
#include <MPU6050.h> //for the MPU6050 accelerometer
#include <wiringPi.h> //used for the delay() function

#include "Content_Model.h"
#include "Visitor_Model.h"


class Content_Controller {
    private:
        std::vector< Content_Model* > content_models;
        Visitor_Model* visitor_model;
        Content_Model* content_model;

    public:

        /**
         * method Content_Controller()
         * Class constructor, sets up the models and NFC reader
         */
        Content_Controller();

        /**
         * method ~Content_Controller()
         * class destructor
         */
        ~Content_Controller();

        /**
         * method populate_from_db()
         * populates the content_models vector with completed Content_Model s
         * @return int 
         */
        int populate_from_db();

        /**
         * method get_nfc_ID()
         * prompts the NFC library to read the NFC tag and return the tag ID
         * Based heavily on https://github.com/Nigh/RC522-raspberrypi/blob/master/nfc.c
         * @return std::string nfc_id which contains the tag ID
         */
        std::string get_nfc_ID();

        /**
         * method scan_tag()
         * Processes the user interaction for scanning a NFC tag and handles appropriate actions afterwards
         * @return int 
         */
        int scan_tag();

        /**
         * method update_db()
         * prompts the model to read the published content JSON file and update the db
         * @return int 
         */
        int update_db();

        /**
         * method get_current_status()
         * prompts the Model to read the device status
         * @return int 
         */
        int get_current_status();

        /**
         * method get_gesture()
         * Accesses the accelerometer library to get the accelerometer stats and turn them into gesture IDs
         * @return int gesture ID number
         */
        int get_gesture();
};

#endif // Content_Controller_h

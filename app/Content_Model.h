#ifndef Content_Model_h
#define Content_Model_h

#include <string.h>
#include <iostream>
#include <jsoncpp/json/json.h> //for handling JSON
#include <fstream> //used for file handling (including JSON)
#include <sqlite3.h> //for the database interaction
#include <SFML/Audio.hpp> //for music/audio playback of .wav files

class Content_Model {
    private:
        //ID numbers to be stored in the model. IDs are populated from the database
        int content_id=0;
        std::string tag_id;
        int next_content=0;
        int gesture_id=0;
        int item_id=0;

        //db file name
        const char* db_name;

        //file locations of the JSON
        std::string new_content_json;
        std::string status_json;
    public:
        /**
         * method Content_Model()
         * default constructor
         */
        Content_Model();


        /**
         * method ~Content_Model()
         * default desstructor
         */
        ~Content_Model();

        /**
         * method save_new_content_from_json()
         * opens the published_content JSON file and replaces the data in the database with the contents of the file
         * @return int
         */
        int save_new_content_from_json();

        /**
         * method get_all_ids_from_db()
         * Gets all of the content IDs from the database. This is used to populate a vector of models
         * @return vector vec_content_ids A vector of content_id s
         */
        std::vector<int> get_all_ids_from_db();

        /**
         * method populate_from_db()
         * populates this model with the data from the datbase. One model = one content row from the db 
         *  based heavily on code from https://stackoverflow.com/a/31747742/2747620
         * @param int content_id 
         * @return int
         */
        int populate_from_db(int content_id);

        /**
         * method get_tag_id()
         * @return std::string tag_id - the ID of the NFC tag stored in the Model (ultimately from the database)
         */
        std::string get_tag_id();

        /**
         * method get_item_id()
         * @return int item_id - the ID of the item stored in the Model (ultimately from the database)
         */
        int get_item_id();

        /**
         * method get_content_id()
         * @param  std::string 
         * @return int content_id - the ID of the content stored in the Model (ultimately from the database)
         */
        int get_content_id();

        /**
         * method get_gesture_id()
         * @param  std::string 
         * @return int gesture_id - the ID of the gesture stored in the Model (ultimately from the database)
         */
        int get_gesture_id();

        /**
         * method get_current_status()
         * Gets the current device status from the status JSON
         * @param  std::string 
         * @return int 
         */
        int get_current_status();

        /**
         * method get_next_content()
         * @return int next_content - the ID of the next_content (to be played) stored in the Model (ultimately from the database)
         */
        int get_next_content();

        /**
         * method update_device_status()
         * Overwrites the status JSON file with the status number passed to this method
         */
        void update_device_status(int status);
};
#endif // Content_Model_h

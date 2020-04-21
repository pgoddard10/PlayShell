#ifndef Content_Model_h
#define Content_Model_h

#include <string.h>
#include <iostream>
#include <jsoncpp/json/json.h> //for handling JSON
#include <fstream> //used for file handling (including JSON)
#include <sqlite3.h> //for the database interaction
#include <SFML/Audio.hpp> //for music/audio playback of .wav files
#include <wiringPi.h> //used for the delay() function


class Content_Model {
    private:
        int content_id;
        std::string tag_id;
        int next_content;
        int gesture_id;
        int item_id;
        std::string new_content_json = "cms_data_exchange/published_content.json";
        std::string status_json = "cms_data_exchange/status.json";
        std::string incoming_visitor_json = "cms_data_exchange/incoming_visitor_id.json";
    public:
        Content_Model();
        ~Content_Model();
        int save_new_content_json();
        std::vector<int> get_all_ids_from_db();
        int populate_from_db(int content_id);
        std::string get_tag_id();
        int get_item_id();
        int get_content_id();
        int get_current_status();
        int get_current_visitor();
};
#endif // Content_Model_h

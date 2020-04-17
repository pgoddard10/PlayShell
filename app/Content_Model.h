#ifndef Content_Model_h
#define Content_Model_h

#include <string.h>
#include <iostream>
#include <jsoncpp/json/json.h> //for handling JSON
#include <fstream> //used for file handling (including JSON)

class Content_Model {
    private:
        int item_id = -1;
        bool item_active = NULL;
        bool content_active = NULL;
        int content_id = -1;
        std::string name = NULL;
        int next_content_id = -1;
        std::string last_modified = NULL;
        int gesture_id = -1;
        std::string new_content_json = "cms_data_exchange/published_content.json";
    public:
        Content_Model();
        ~Content_Model();
        Json::Value read_new_content_json();
};

#endif // Content_Model_h

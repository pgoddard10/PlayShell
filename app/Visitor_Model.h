#ifndef Visitor_Model_h
#define Visitor_Model_h

#include <string.h>
#include <iostream>
#include <jsoncpp/json/json.h> //for handling JSON
#include <fstream> //used for file handling (including JSON)
#include <sqlite3.h> //for the database interaction


class Visitor_Model {
    
    private:
        std::string incoming_visitor_json = "cms_data_exchange/incoming_visitor_id.json";
        std::string outgoing_visitor_data = "cms_data_exchange/outgoing_visitor_data.json";
        const char* db_name = "audio_culture.db";
        int visitor_id = -1;
        int set_current_visitor();

    public:
        Visitor_Model();
        ~Visitor_Model();
        int save_visitor_details_as_json();
        int save_visitor_interaction(int content_id);


};

#endif // Visitor_Model_h


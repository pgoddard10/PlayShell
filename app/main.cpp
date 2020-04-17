
#include <stdio.h>
#include <stdint.h>
#include <stdbool.h>
#include <string.h>
#include <iostream>
#include <fstream>
#include <jsoncpp/json/json.h> //for handling JSON
#include "Content_View.h"

#define DEVICE_STATUS_READY 0
#define DEVICE_STATUS_IN_USE 1
#define DEVICE_STATUS_DEVICE_UPDATING 2
#define DEVICE_STATUS_CMS_UPDATING 3

std::string status_json = "cms_data_exchange/status.json";
std::string incoming_visitor_id = "cms_data_exchange/incoming_visitor_id.json";
std::string outgoing_visitor_data = "cms_data_exchange/outgoing_visitor_data.json";

Content_View* content_view = new Content_View(); //<---- something is up with this

int main() {
    while(1) {
        int current_status = -1;
        //read the JSON file and get the content ID
        std::ifstream ifs_status_json(status_json);
        if(ifs_status_json.is_open()) { //only continue if the file is found
            Json::Reader reader;
            Json::Value obj;
            reader.parse(ifs_status_json, obj);
            // std::string content_id_str = obj["status"]["code"].asString();
            current_status = obj["status"]["code"].asInt();
            ifs_status_json.close(); //close the file handler
        }
        
        content_view->populate_from_db();
        if(current_status==DEVICE_STATUS_READY) {
            content_view->update_db();
            break;
        }
        else if(current_status==DEVICE_STATUS_IN_USE) {
            std::cout << "get scanning the tags, etc" << std::endl;
            content_view->scan_tag();
        }
    }
    return 0;
}
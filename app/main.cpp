
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

std::string outgoing_visitor_data = "cms_data_exchange/outgoing_visitor_data.json";

Content_View* content_view = new Content_View();

int main() {
    while(1) {
        int current_status = content_view->get_current_status(); //get the device status from status.json
        content_view->populate_from_db();
        if(current_status==DEVICE_STATUS_DEVICE_UPDATING) {
            //trigger a database update
            content_view->update_db();
        }
        else if(current_status==DEVICE_STATUS_IN_USE) {
            std::cout << "get scanning the tags, etc" << std::endl;
            content_view->scan_tag();
        }
        else if(current_status==DEVICE_STATUS_READY) {
            //the device is not wanted right now :'(
            //wait for the CMS to give a command
            std::cout << "Device is ready for a command from the CMS" << std::endl;
            delay(10000); // 10 seconds (let's not drain the battery)
        }
        else {
            //error state. This should never happen and a sync with the CMS should fix it.
            std::cout << "Current status is not recognised. The status number is " << current_status << std::endl;
            delay(5000); // 5 seconds
        }
    }
    return 0;
}
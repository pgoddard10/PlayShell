#include "Content_View.h"


#define DEVICE_STATUS_READY 0
#define DEVICE_STATUS_IN_USE 1
#define DEVICE_STATUS_DEVICE_UPDATING 2
#define DEVICE_STATUS_CMS_UPDATING 3

/**
 * method Content_View()
 * default constructor
 */
Content_View::Content_View() {
    content_controller = new Content_Controller();
}

/**
 * method ~Content_View()
 * default desstructor
 */
Content_View::~Content_View() {
}

/**
 * method run()
 * Starts an infinite loop which flicks between device statuses. Calls the main function for those that have actions
 */
void Content_View::run() {
    while(1) {
        int current_status = (*content_controller).get_current_status(); //get the device status from status.json
        (*content_controller).populate_from_db();
        if(current_status==DEVICE_STATUS_DEVICE_UPDATING) {
            //trigger a database update
            std::cout << "Device is updating the database. Please wait..." << std::endl;
            (*content_controller).update_db();
        }
        else if(current_status==DEVICE_STATUS_IN_USE) {
            //visitor goes out and has some fun scanning tags, listening to audio etc.
            std::cout << "Please start scanning tags..." << std::endl;
            (*content_controller).scan_tag();
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
}
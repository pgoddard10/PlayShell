#ifndef Content_Controller_h
#define Content_Controller_h

#include <vector>
#include <string.h>
#include "RC522.h" //for NFC scanning
#include <sqlite3.h> //for the database interaction

#include "Content_Model.h"
#include "Visitor_Model.h"


class Content_Controller {
    private:
        std::vector< Content_Model* > content_models;
        Visitor_Model* visitor_model;

    public:
        Content_Controller();
        ~Content_Controller();
        int populate_from_db();
        std::string get_nfc_ID();
        int scan_tag();
        int update_db();
        int get_current_status();
        int save_visitor_details_as_json();
};

#endif // Content_Controller_h

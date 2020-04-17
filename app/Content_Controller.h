#ifndef Content_Controller_h
#define Content_Controller_h

#include <vector>
#include <string.h>
#include "RC522.h" //for NFC scanning
#include "Content_Model.h"
#include "Visitor_Model.h"



class Content_Controller {
    private:
        int update_db();
        std::vector< Content_Model* > content_models;
        std::vector< Visitor_Model* > visitor_models;

    public:
        Content_Controller();
        ~Content_Controller();
        std::string get_nfc_ID();
};

#endif // Content_Controller_h

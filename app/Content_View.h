#ifndef Content_View_h
#define Content_View_h

#include <iostream>
#include "Content_Controller.h"

class Content_View {

    private:
        int update_db();

    public:
        Content_View();
        ~Content_View();
        Content_Controller* content_controller;// = new Content_Controller();
        int get_nfc_ID();
};

#endif // Content_View_h

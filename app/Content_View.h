#ifndef Content_View_h
#define Content_View_h

#include <iostream>
#include "Content_Controller.h"

class Content_View {

    private:

    public:
        Content_View();
        ~Content_View();
        Content_Controller* content_controller;// = new Content_Controller();
        int scan_tag();
        int update_db();
        int populate_from_db();
};

#endif // Content_View_h

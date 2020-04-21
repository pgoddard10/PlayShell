#ifndef Content_View_h
#define Content_View_h

#include <iostream>
#include "Content_Controller.h"

class Content_View {

    private:

    public:
        Content_View();
        ~Content_View();
        Content_Controller* content_controller;
        void run();
};

#endif // Content_View_h

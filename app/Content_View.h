#ifndef Content_View_h
#define Content_View_h

#include <iostream>
#include "Content_Controller.h"

class Content_View {

    private:
        Content_Controller* content_controller;

    public:
    
        /**
         * method Content_View()
         * default constructor
         */
        Content_View();

        /**
         * method ~Content_View()
         * default desstructor
         */
        ~Content_View();

        /**
         * method run()
         * Starts an infinite loop which flicks between device statuses. Calls the main function for those that have actions
         */
        void run();
};

#endif // Content_View_h
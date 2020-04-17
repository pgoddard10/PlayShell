#include "Content_View.h"

Content_View::Content_View() {
    content_controller = new Content_Controller();
}

Content_View::~Content_View() {
}


int Content_View::update_db() {
    std::cout << "hello - this is int Content_View::update_db()" << std::endl;

    // this->content_controller->update_db();
    
    return 0;
}


int Content_View::get_nfc_ID() {
    (*content_controller).get_nfc_ID();
    
    return 0;
}

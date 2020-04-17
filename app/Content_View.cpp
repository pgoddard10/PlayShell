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


int Content_View::scan_tag() {
    (*content_controller).play_content();
    
    return 0;
}
int Content_View::populate_from_db() {
    (*content_controller).populate_from_db();
    
    return 0;
}
#include "Content_View.h"

Content_View::Content_View() {
    content_controller = new Content_Controller();
}

Content_View::~Content_View() {
}


int Content_View::update_db() {
    (*content_controller).update_db();
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
int Content_View::get_current_status() {
    return (*content_controller).get_current_status();
}

#include <stdio.h>
#include <stdint.h>
#include <stdbool.h>
#include <string.h>
#include <iostream>
#include <fstream>
#include <jsoncpp/json/json.h> //for handling JSON
#include "Content_View.h"


Content_View* content_view = new Content_View();

int main() {
    content_view->run();
    return 0;
}
#include "Content_Controller.h"

Content_Controller::Content_Controller() {
	RC522_setup(7);
	PcdReset ();
	M500PcdConfigISOType('A');
}

Content_Controller::~Content_Controller() {
}



int Content_Controller::update_db() {
    std::cout << "hello - this is int Content_Controller::update_db()" << std::endl;


    //loop through json, sanity check stuff
    //call Model to update the database



        // for (Json::Value::ArrayIndex i = 0; i != obj.size(); i++)
        //     if (obj[i].isMember("attr1"))
        //         values.push_back(obj[i]["attr1"].asString());

        
        // current_status = obj["status"]["code"].asInt();

    return 0;
}


int Content_Controller::populate_from_db() {
    Content_Model* tmp_model = new Content_Model();
    std::vector<int> content_ids;
    content_ids = (*tmp_model).get_all_ids_from_db();
    // std::cout << "The number of content IDs found is: " << content_ids.size() << std::endl;
    for(std::vector<int> :: iterator it = content_ids.begin(); it != content_ids.end(); ++it){
        Content_Model* new_content_model = new Content_Model();
        (*new_content_model).populate_from_db(*it);
        this->content_models.push_back(new_content_model);
    }
    return 0;
}

std::string Content_Controller::get_nfc_ID(){
	char cStr [ 30 ];
	uint8_t ucArray_ID [ 4 ]; //IC card type and UID (IC card serial number)
	uint8_t ucStatusReturn; //Return status
    std::string nfc_id;
    if ( ( ucStatusReturn = PcdRequest ( PICC_REQALL, ucArray_ID ) ) != MI_OK ) { // If you fail to find the card again
        ucStatusReturn = PcdRequest ( PICC_REQALL, ucArray_ID );
    }

    if ( ucStatusReturn == MI_OK  ) {
        // Anti-collision (when multiple cards enter the reader's operating range, the anti-collision mechanism will choose one of them to operate)
        if ( PcdAnticoll ( ucArray_ID ) == MI_OK ) {
            sprintf ( cStr, "%02X%02X%02X%02X",
                        ucArray_ID [ 0 ],
                        ucArray_ID [ 1 ],
                        ucArray_ID [ 2 ],
                        ucArray_ID [ 3 ] );
            nfc_id = std::string(cStr);
        }
    }
    return nfc_id;
}

int Content_Controller::play_content() {
    std::string nfc_tag_id;
	while (nfc_tag_id.empty()) {
        nfc_tag_id = this->get_nfc_ID();
	}
    for(uint i=0; i < this->content_models.size() ; i++ ) {
        if(nfc_tag_id.compare((*this->content_models[i]).get_tag_id()) == 0) {
            std::cout << "They match! " << nfc_tag_id << std::endl;
            int item_id = (*this->content_models[i]).get_item_id();
            int content_id = (*this->content_models[i]).get_content_id();

            std::cout << "cms_data_exchange/audio/" << item_id << "/" << content_id << "/sound.wav" << std::endl;
            std::string filename = "cms_data_exchange/audio/" + std::to_string(item_id) + "/" + std::to_string(content_id) + "/sound.wav";
            sf::Music buffer;
            if (!buffer.openFromFile(filename)) {
                std::cout << "error loading file: '" << filename << "'" << std::endl;
                return -1; // error
            }
            buffer.play();
            float duration = buffer.getDuration().asSeconds();
            duration = (duration * 1000)+1000;
            std::cout << "file duration: " << duration << std::endl;
            delay(duration); //uses the WiringPi include
            //*****
            //    now play the next content or wait for a gesture!!
            //*****
        }
    }
    return 0;
}
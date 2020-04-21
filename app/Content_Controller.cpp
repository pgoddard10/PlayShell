#include "Content_Controller.h"
#define CHECK_IN_TAG "88042199"

Content_Controller::Content_Controller() {
	RC522_setup(7);
	PcdReset ();
	M500PcdConfigISOType('A');
    visitor_model = new Visitor_Model();
}

Content_Controller::~Content_Controller() {
}

int Content_Controller::get_current_status() {
    Content_Model* tmp_model = new Content_Model();
    return (*tmp_model).get_current_status();
}

int Content_Controller::update_db() {
    Content_Model* tmp_model = new Content_Model();
    (*tmp_model).save_new_content_json();
    return 0;
}


int Content_Controller::populate_from_db() {
    Content_Model* tmp_model = new Content_Model();
    std::vector<int> content_ids;
    content_ids = (*tmp_model).get_all_ids_from_db();
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

int Content_Controller::scan_tag() {
    sf::Music buffer; //reusable sound buffer

    //force a continuous loop until the NFC tag assigned to finish with the device is scanned
    std::string nfc_tag_id;

    while(nfc_tag_id.compare(CHECK_IN_TAG) != 0) {
        nfc_tag_id = "";
        while (nfc_tag_id.empty()) {    //loop until a tag is scanned
            nfc_tag_id = this->get_nfc_ID();
        }

        //stop any existing sound playback
        buffer.stop(); //works as an interrupt: if a new tag is scanned, stop playing the previous tag

        //loop through all of the content (i.e. available tags)
        for(uint i=0; i < this->content_models.size() ; i++ ) {

            //if this model's tag_id matches the one scanned
            if(nfc_tag_id.compare((*this->content_models[i]).get_tag_id()) == 0) {
                //grab the associated item and content IDs
                //these form part of the file location
                int item_id = (*this->content_models[i]).get_item_id();
                int content_id = (*this->content_models[i]).get_content_id();

                std::cout << "cms_data_exchange/audio/" << item_id << "/" << content_id << "/sound.wav" << std::endl;
                std::string filename = "cms_data_exchange/audio/" + std::to_string(item_id) + "/" + std::to_string(content_id) + "/sound.wav";

                //save the visitor interaction in the db
                (*this->visitor_model).save_visitor_interaction(content_id);

                //load the sound from the file location into the buffer
                if (!buffer.openFromFile(filename)) {
                    return -1; // error loading file
                }

                buffer.play(); //play the sound that's now loaded in the buffer

                //**
                // WAIT FOR GESTURE FUNCTION CALL GOES HERE - See Sprint 3
                //**

                // now play the next content, if there is one
                int next_content = (*this->content_models[i]).get_next_content();
                if(next_content > 0) {

                    float duration = buffer.getDuration().asSeconds();
                    duration = (duration * 1000)+1000;
                    delay(duration); //wait for the previous sound to finish playing

                    std::cout << "cms_data_exchange/audio/" << item_id << "/" << next_content << "/sound.wav" << std::endl;
                    std::string filename = "cms_data_exchange/audio/" + std::to_string(item_id) + "/" + std::to_string(next_content) + "/sound.wav";

                    //save the visitor interaction in the db
                    (*this->visitor_model).save_visitor_interaction(content_id);

                    //load the sound from the file location into the buffer
                    if (!buffer.openFromFile(filename)) {
                        return -1; // error
                    }

                    buffer.play(); //play the sound that's now loaded in the buffer
                }
            }
        }
    }
    //stop any playback when exiting the loop
    buffer.stop();
    (*this->visitor_model).save_visitor_details_as_json();
    (*this->content_models[0]).update_device_status(0);
    return 0;
}
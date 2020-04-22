/**
 * Class Content_Controller
 * Responsible for processing the content related data, including linking it to the NFC library
 *
 * @author	Paul Goddard
 * 			paul2.goddard@live.uwe.ac.uk
 * 			https://github.com/pgoddard10/
 * 			https://www.linkedin.com/in/pgoddard10/
 * 			https://twitter.com/pgoddard10
 * @date Spring 2020 
 */

#include "Content_Controller.h"
#define CHECK_IN_TAG "88042199"

/**
 * method Content_Controller()
 * Class constructor, sets up the models and NFC reader
 */
Content_Controller::Content_Controller() {
	RC522_setup(7);
	PcdReset ();
	M500PcdConfigISOType('A');
    this->visitor_model = new Visitor_Model();
    this->content_model = new Content_Model();
}
/**
 * method ~Content_Controller()
 * class destructor
 */
Content_Controller::~Content_Controller() {
}
/**
 * method get_current_status()
 * prompts the Model to read the device status
 * @return int 
 */
int Content_Controller::get_current_status() {
    return (*this->content_model).get_current_status();
}
/**
 * method update_db()
 * prompts the model to read the published content JSON file and update the db
 * @return int 
 */
int Content_Controller::update_db() {
    (*this->content_model).save_new_content_from_json();
    return 0;
}

/**
 * method populate_from_db()
 * populates the content_models vector with completed Content_Model s
 * @return int 
 */
int Content_Controller::populate_from_db() {
    std::vector<int> content_ids;
    content_ids = (*this->content_model).get_all_ids_from_db();
    for(std::vector<int> :: iterator it = content_ids.begin(); it != content_ids.end(); ++it){
        Content_Model* new_content_model = new Content_Model();
        (*new_content_model).populate_from_db(*it);
        this->content_models.push_back(new_content_model);
    }
    return 0;
}

/**
 * method get_nfc_ID()
 * prompts the NFC library to read the NFC tag and return the tag ID
 * Based heavily on https://github.com/Nigh/RC522-raspberrypi/blob/master/nfc.c
 * @return std::string nfc_id which contains the tag ID
 */
std::string Content_Controller::get_nfc_ID(){
	char cStr [ 30 ];
	uint8_t ucArray_ID [ 4 ]; //IC card type and UID (IC card serial number)
	uint8_t ucStatusReturn; //Return status
    std::string nfc_id;

    // If you fail to find the card, try again
    if ( ( ucStatusReturn = PcdRequest ( PICC_REQALL, ucArray_ID ) ) != MI_OK ) { 
        ucStatusReturn = PcdRequest ( PICC_REQALL, ucArray_ID );
    }

    if ( ucStatusReturn == MI_OK  ) {
        // Anti-collision (when multiple cards enter the reader's operating range,
        //        the anti-collision mechanism will choose one of them to operate)
        if ( PcdAnticoll ( ucArray_ID ) == MI_OK ) {
            sprintf ( cStr, "%02X%02X%02X%02X",
                        ucArray_ID [ 0 ],
                        ucArray_ID [ 1 ],
                        ucArray_ID [ 2 ],
                        ucArray_ID [ 3 ] );
            nfc_id = std::string(cStr); //store the tag ID
        }
    }
    return nfc_id;
}

/**
 * method scan_tag()
 * Processes the user interaction for scanning a NFC tag and handles appropriate actions afterwards
 * @return int 
 */
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
                int gesture_id = (*this->content_models[i]).get_gesture_id();

                std::string filename = "cms_data_exchange/audio/" + std::to_string(item_id) + "/" + std::to_string(content_id) + "/sound.wav";

                //save the visitor interaction in the db
                (*this->visitor_model).save_visitor_interaction(content_id);

                //load the sound from the file location into the buffer
                if (!buffer.openFromFile(filename)) {
                    return -1; // error loading file
                }

                buffer.play(); //play the sound that's now loaded in the buffer

                if(gesture_id > 0) {

                    float duration = buffer.getDuration().asSeconds();
                    duration = (duration * 1000)+1000;
                    delay(duration); //wait for the previous sound to finish playing

                    while(gesture_id != this->get_gesture()) {
                        
                    }
                }

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


MPU6050 accelerometer(0x68);
/**
 * method get_gesture()
 * Accesses the accelerometer library to get the accelerometer stats and turn them into gesture IDs
 * @return int gesture_id gesture ID number
 */
int Content_Controller::get_gesture() {
	
    //set up new instance of the accelerometer
    int gesture_id = 0;

    float x, y, z; //variables used in the shake

    delay(1000); //Wait for the MPU6050 to stabilize
	
	accelerometer.calc_yaw = true;

    accelerometer.getAccel(&x, &y, &z); //for the shake (on z axis)
    
    if(z > 1.3) {
        gesture_id = 1;
    }

    delay(250); //0.25 sec

	return gesture_id;
}
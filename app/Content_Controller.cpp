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

std::string Content_Controller::get_nfc_ID(){
	char cStr [ 30 ];
	uint8_t ucArray_ID [ 4 ]; //IC card type and UID (IC card serial number)
	uint8_t ucStatusReturn; //Return status
	while ( 1 ) {
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
				return cStr;
			}
		}
	}
    // return cStr; //ONLY FOR TESTING - REMOVE WHEN UNCOMMENTING THE ABOVE
}
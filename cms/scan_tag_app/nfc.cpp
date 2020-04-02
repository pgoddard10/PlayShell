/**
 *  @Author: Paul Goddard - 17019749
 *  @Date: Winter/Spring 2019-2020
 *  @Description: 
 *
 */
 
#include "nfc.h"

/** constructor, as per default settings */
NFC::NFC() {
	RC522_setup(7);
	PcdReset ();
	M500PcdConfigISOType('A');
}

/** deconstructor, as per default settings */
NFC::~NFC() {
}

std::string NFC::get_nfc_ID(){
	char cStr [ 30 ];
	uint8_t ucArray_ID [ 4 ]; //IC card type and UID (IC card serial number)
	uint8_t ucStatusReturn; //Return status
	while ( 1 ) {
		/*寻卡*/
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
}
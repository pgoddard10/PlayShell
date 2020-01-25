/**
 *  @Author: Paul Goddard - 17019749
 *  @Date: Winter/Spring 2019-2020
 *  @Description: 
 *
 */

#ifndef NFC_H
#define NFC_H

#include <iostream>
#include <string.h>
#include "RC522.h" //for NFC scanning

class NFC {

	public:
		NFC(); /** constructor, as per default settings */
		~NFC(); /** deconstructor, as per default settings */
		std::string get_nfc_ID();
		
	private:
		//something
};

#endif // NFC_H

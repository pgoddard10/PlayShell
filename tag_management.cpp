/**
 *  @Author: Paul Goddard - 17019749
 *  @Date: Winter/Spring 2019-2020
 *  @Description: 
 *
 */
 
#include "tag_management.h"

/** constructor, as per default settings */
Tag_management::Tag_management() {
	// this->tts = TTS();
	// this->wav = WAV();
	// this->db = Database("audio_culture.db");
	// this->nfc = NFC();
}

/** deconstructor, as per default settings */
Tag_management::~Tag_management() {
}

int Tag_management::manage(){
	std::cout << "Would you like to ADD, EDIT or DELETE the content related to a tag?" << std::endl;
	std::string input = "";
	std::getline (std::cin,input);
	const char* choice = input.c_str();
	if(strcmp(choice,"ADD")==0) {
		this->add_tag();
	}
	else if(strcmp(choice,"EDIT")==0) {
		this->edit_tag();
	}
	if(strcmp(choice,"DELETE")==0) {
		this->delete_tag();
	}
	return 0;
}

int Tag_management::add_tag(){
	std::cout << "Please scan the NFC tag" << std::endl;
	std::string nfcID = "";
	nfcID = this->nfc.get_nfc_ID();
	if(this->db.does_tag_exist(nfcID)) {
		std::cout << "This tag alread exists, do you want to EDIT or CANCEL?" << std::endl;
		std::string input = "";
		std::getline (std::cin,input);
		const char* edit_or_cancel = input.c_str();
		if(strcmp(edit_or_cancel,"EDIT")!=0) {
			return -1;
		}
	}
	std::cout << "Enter the text you want to store against this tag" << std::endl;
	std::string desc = "";
	std::getline (std::cin,desc);
	
	std::cout << "Please wait whilst we generate the sound file for your text" << std::endl;
	this->tts.create_tts(desc, true, nfcID);
	return 0;
}

int Tag_management::edit_tag(){
	return 0;
}
int Tag_management::delete_tag(){
	return 0;
}


/*
add / edit process:
 does tag exist?
 if so,
   ask "edit or cancel?"
 if edit:
	enter new text:
	create new .wav
 present user choices
 
delete process:
 does tag exist?
 if so,
   present content to user
   "confirm delete, or cancel?"
   remove from db
   remove .wav
   confirm
 present user choices
 */
/**
 *  @Author: Paul Goddard - 17019749
 *  @Date: Winter/Spring 2019-2020
 *  @Description: 
 *
 */
 
#include "tag_management.h"

/** constructor, as per default settings */
Tag_management::Tag_management() {
}

/** deconstructor, as per default settings */
Tag_management::~Tag_management() {
}

int Tag_management::manage(){
	std::cout << "Would you like to ADD or DELETE the content related to a tag? or EXIT the program." << std::endl;
	std::string input = "";
	std::getline (std::cin,input);
	std::for_each(input.begin(), input.end(), [](char & c) {
		c = ::toupper(c);
	});
	const char* choice = input.c_str();
	if(strcmp(choice,"ADD")==0) {
		this->add_tag();
	}
	else if(strcmp(choice,"DELETE")==0) {
		this->delete_tag();
	}
	else if(strcmp(choice,"EXIT")==0) {
		return -1;
	}
	return 0;
}

int Tag_management::add_tag(){
	std::cout << "Please scan the NFC tag" << std::endl;
	std::string nfcID = "";
	nfcID = this->nfc.get_nfc_ID();
	if(this->db.does_tag_exist(nfcID)) {
		std::cout << "This tag already exists. Cancelling action." << std::endl;
		return -1;
	}
	std::cout << "Enter the text you want to store against this tag" << std::endl;
	std::string desc = "";
	std::getline (std::cin,desc);
	
	if(this->db.save_desc(nfcID, desc)!=0) { //save desc into database
		std::cout << "Something went wrong saving the description for this tag. Please try again later" << std::endl;
		return -1;
	}
	std::cout << "Please wait whilst we generate the sound file for your text" << std::endl;
	this->tts.create_tts(desc, true, nfcID);
	return 0;
}

int Tag_management::delete_tag(){
	std::cout << "Please scan the NFC tag" << std::endl;
	std::string nfcID = "";
	nfcID = this->nfc.get_nfc_ID();
	if(this->db.does_tag_exist(nfcID)) {
		std::string desc = this->db.get_tag_desc(nfcID);
		std::cout << "The description attached to this tag is: \n\n+=+=+=+=+=+=+=+=+=+=+=+=+=\n" << desc << "\n+=+=+=+=+=+=+=+=+=+=+=+=+=\n" << std::endl;
		std::cout << "Are you sure you want to delete this? YES/NO" << std::endl;
		std::string confirm = "";
		std::getline (std::cin,confirm);
		std::for_each(confirm.begin(), confirm.end(), [](char & c) {
			c = ::toupper(c);
		});
		const char* choice = confirm.c_str();
		if(strcmp(choice,"YES")==0) {
			//delete
			this->db.delete_tag(nfcID);
			std::string filename = nfcID + ".wav";
			this->wav.delete_file(filename);
		}
	}
	else {
		std::cout << "The scanned tag does not contain any content..." << std::endl;
	}
	return 0;
}
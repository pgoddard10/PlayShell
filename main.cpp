#include <stdio.h>
#include <stdint.h>
#include <stdbool.h>
#include <string.h>
#include <iostream>

#include "database.h" //for database interaction
#include "tts.h" //for Text-To-Speech (TTS) interaction
#include "wav.h" //for .wav file interaction
#include "nfc.h" //for .wav file interaction

int main(int argc, char** argv) {
	TTS tts = TTS();
	WAV wav = WAV();
	Database db = Database();
	NFC nfc = NFC();

	bool use_tts = true;
	bool save_as_wav = false;
	if(argc > 1) {
		if((strcmp(argv[1],"tts")==0) && (strcmp(argv[2],"play")==0)) {
			use_tts = true;
			save_as_wav = false;
		}
		else if((strcmp(argv[1],"tts")==0) && (strcmp(argv[2],"wav")==0)) {
			use_tts = true;
			save_as_wav = true;
		}
		else if((strcmp(argv[1],"wav")==0) && (strcmp(argv[2],"play")==0)) {
			use_tts = false;
			save_as_wav = false; //N/A
		}
		else {
			fprintf(stderr,"usage: tts play (default) OR tts wav OR wav play\n\n");
			return 1;
		}
	}
	
	std::string welcome_msg = "Please scan a tag.";
	if(use_tts) {
		tts.create_tts(welcome_msg, save_as_wav, "please_scan_a_tag");
	}
	else {
		wav.play_wav("sounds/please_scan_a_tag.wav");
	}
	std::cout << welcome_msg << std::endl;
	while(1) {
		std::string nfcID = "";
		nfcID = nfc.get_nfc_ID();
		if(nfcID.length() > 0) {
			std::cout << "Tag successfully scanned: " << nfcID << " ... searching for matching record..." << std::endl;
			
			if(use_tts) {
				std::string desc = db.get_tag_desc(nfcID);
				tts.create_tts(desc, save_as_wav, nfcID);
			}
			else {
				std::string soundfile = "sounds/" + nfcID + ".wav";
				std::cout << soundfile << std::endl;
				wav.play_wav(soundfile);
			}
			
			std::string scan_another_msg = "Please scan another tag.";
			if(use_tts) {
				tts.create_tts(scan_another_msg, save_as_wav, "please_scan_another_tag");
			}
			else {
				wav.play_wav("sounds/please_scan_another_tag.wav");
			}
			std::cout << scan_another_msg << std::endl;
		}
	}

	return (0);
}

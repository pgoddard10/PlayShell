#include <stdio.h>
#include <stdint.h>
#include <stdbool.h>
#include <string.h>
#include <iostream>

#include "database.h" //for database interaction
#include "tts.h" //for Text-To-Speech (TTS) interaction
#include "wav.h" //for .wav file interaction
#include "nfc.h" //for NFC tag interaction
#include "tag_management.h" //for NFC tag interaction
#include <MPU6050.h>

MPU6050 device(0x68);

int main(int argc, char** argv) {
	TTS tts = TTS();
	WAV wav = WAV();
	Database db = Database("audio_culture.db");
	NFC nfc = NFC();
	Tag_management tag_management = Tag_management();
	
	float ax, ay, az, gr, gp, gy; //Variables to store the accel, gyro and angle values

	sleep(1); //Wait for the MPU6050 to stabilize
	
	device.calc_yaw = true;


	

	bool use_tts = false;
	bool save_as_wav = false;
	bool manage = false;
	if(argc > 1) {
		if((strcmp(argv[1],"tts")==0) && (strcmp(argv[2],"play")==0)) {
			use_tts = true;
			save_as_wav = false;
		}
		else if((strcmp(argv[1],"tts")==0) && (strcmp(argv[2],"wav")==0)) {
			use_tts = true;
			save_as_wav = true;
			std::cout << "-- No sound will be played during this program run. --" << std::endl;
		}
		else if((strcmp(argv[1],"wav")==0) && (strcmp(argv[2],"play")==0)) {
			use_tts = false;
			save_as_wav = false; //N/A
		}
		else if((strcmp(argv[1],"manage")==0)) {
			manage = true;
		}
		else {
			fprintf(stderr,"usage: wav play (default) OR tts wav OR tts play\n\n");
			return 1;
		}
	}
	if(manage) {
		while(1) {
			if(tag_management.manage()!=0) {
				break;
			}
		}
	}
	else {
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
					if(db.does_tag_exist(nfcID)) {
						std::string desc = db.get_tag_desc(nfcID);
						tts.create_tts(desc, save_as_wav, nfcID);
					}
					else {
						std::cout << "There is no stored content for that tag." << std::endl;
					}
				}
				else {
					std::string soundfile = "sounds/" + nfcID + ".wav";
					std::cout << soundfile << std::endl;
					wav.play_wav(soundfile);
				}
				
				for (int i = 0; i < 5; i++) {
					device.getAngle(0, &gr);
					device.getAngle(1, &gp);
					device.getAngle(2, &gy);
					std::cout << "Current angle around the roll axis: " << gr << "\n";
					std::cout << "Current angle around the pitch axis: " << gp << "\n";
					std::cout << "Current angle around the yaw axis: " << gy << "\n";
					usleep(250000); //0.25sec
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
	}

	return (0);
}

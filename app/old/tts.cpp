/**
 *  @Author: Paul Goddard - 17019749
 *  @Date: Winter/Spring 2019-2020
 *  @Description: 
 *
 */
 
#include "tts.h"

/** constructor, as per default settings */
TTS::TTS() {
}

/** deconstructor, as per default settings */
TTS::~TTS() {
}

void TTS::create_tts(std::string text, bool as_file, std::string filename) {
	std::string str = "flite -voice cmu_us_slt"; //change from default voice
	if(as_file == true) {
		str = str + " -o sounds/" + filename + ".wav"; //save as file (instead of reading aloud)
	}
	str = str + " -t '" + text + "' --setf duration_stretch=1.25"; //specify the text and slow the voice down
	const char *command = str.c_str(); //convert the string into a char array
	system(command); //run as a system command
}
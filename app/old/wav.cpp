/**
 *  @Author: Paul Goddard - 17019749
 *  @Date: Winter/Spring 2019-2020
 *  @Description: 
 *
 */
 
#include "wav.h"

/** constructor, as per default settings */
WAV::WAV() {
}

/** deconstructor, as per default settings */
WAV::~WAV() {
}

int WAV::play_wav(std::string filename) {
	sf::Music buffer;
	if (!buffer.openFromFile(filename)) {
			std::cout << "error loading file: '" << filename << "'" << std::endl;
			return -1; // error
	}
	buffer.play();
	float duration = buffer.getDuration().asSeconds();
	duration = (duration * 1000)+1000;
	std::cout << duration << std::endl;
	delay(duration); //uses the WiringPi include
	return 0;
}

int WAV::delete_file(std::string filename) {
	std::string path = "rm sounds/" + filename;
	const char *command = path.c_str(); //convert the string into a char array
	system(command);
	return 0;
}
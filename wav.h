/**
 *  @Author: Paul Goddard - 17019749
 *  @Date: Winter/Spring 2019-2020
 *  @Description: 
 *
 */

#ifndef WAV_H
#define WAV_H

#include <iostream>
#include <string.h>
#include <SFML/Audio.hpp> //for music/audio playback of .wav files

#include <wiringPi.h> //used for the delay() function

class WAV {

	public:
		WAV(); /** constructor, as per default settings */
		~WAV(); /** deconstructor, as per default settings */
		int play_wav(std::string filename);

	private:
		//something
};

#endif // WAV_H

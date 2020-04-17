/**
 *  @Author: Paul Goddard - 17019749
 *  @Date: Winter/Spring 2019-2020
 *  @Description: 
 *
 */

#ifndef TTS_H
#define TTS_H

#include <iostream>
#include <string.h>

class TTS {

	public:
		TTS(); /** constructor, as per default settings */
		~TTS(); /** deconstructor, as per default settings */
		void create_tts(std::string text, bool as_file, std::string filename);

	private:
		//something
		 
};

#endif // TTS_H

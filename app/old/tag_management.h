/**
 *  @Author: Paul Goddard - 17019749
 *  @Date: Winter/Spring 2019-2020
 *  @Description: 
 *
 */

#ifndef TAG_MANAGEMENT
#define TAG_MANAGEMENT

#include <iostream>
#include <string.h>
#include "nfc.h"
#include "wav.h"
#include "tts.h"
#include "database.h"

class Tag_management {

	public:
		Tag_management(); /** constructor, as per default settings */
		~Tag_management(); /** deconstructor, as per default settings */
		int manage();
		
	private:
		TTS tts = TTS();
		WAV wav = WAV();
		Database db = Database("audio_culture.db");
		NFC nfc = NFC();
		
		int add_tag();
		int delete_tag();
};

#endif // TAG_MANAGEMENT

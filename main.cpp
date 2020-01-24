#include <stdio.h>
#include <stdint.h>
#include <stdbool.h>
#include <string.h>
#include <iostream>

#include <sqlite3.h>
#include <wiringPi.h>
#include <wiringPiSPI.h>

#include "RC522.c"

void create_tts(std::string text, bool as_file = false, std::string filename = "null") {
	std::string str = "flite -voice cmu_us_slt"; //change from default voice
	if(as_file == true) {
		str = str + " -o sounds/" + filename + ".wav"; //save as file (instead of reading aloud)
	}
	str = str + " -t '" + text + "' --setf duration_stretch=1.25"; //specify the text and slow the voice down
	const char *command = str.c_str(); //convert the string into a char array
	system(command); //run as a system command
}

static int callback(void* data, int argc, char** argv, char** azColName) {
	int i;
	std::string tagID = "";
	for (i = 0;i < argc;i++) {
		if(strcmp(azColName[i],"id")==0) {
			tagID = argv[i];
		}
		else {
			std::cout << "Record found: \n" << argv[i] << std::endl; 
			create_tts(argv[i], true, tagID);
		}
	}
	return 0;
}

std::string get_nfc_ID(){
	char cStr [ 30 ];
	uint8_t ucArray_ID [ 4 ];    /*先后存放IC卡的类型和UID(IC卡序列号)*/
	uint8_t ucStatusReturn;      /*返回状态*/
	while ( 1 ) {
		/*寻卡*/
		if ( ( ucStatusReturn = PcdRequest ( PICC_REQALL, ucArray_ID ) ) != MI_OK ) { /*若失败再次寻卡*/
			ucStatusReturn = PcdRequest ( PICC_REQALL, ucArray_ID );
		}

		if ( ucStatusReturn == MI_OK  ) {
			/*防冲撞（当有多张卡进入读写器操作范围时，防冲突机制会从其中选择一张进行操作）*/
			if ( PcdAnticoll ( ucArray_ID ) == MI_OK ) {
				sprintf ( cStr, "%02X%02X%02X%02X",
				          ucArray_ID [ 0 ],
				          ucArray_ID [ 1 ],
				          ucArray_ID [ 2 ],
				          ucArray_ID [ 3 ] );
				return cStr;
			}
		}
	}
}

int get_desc_from_db(std::string nfcID) {
	sqlite3* DB;
	int exit = sqlite3_open("audio_culture.db", &DB);

	//select tag from database:
	std::string sql("SELECT id, desc FROM tag WHERE ID = '"+nfcID+"';");
	if (exit) {
		std::cerr << "Error open DB " << sqlite3_errmsg(DB) << std::endl;
		return (-1);
	}

	int rc = sqlite3_exec(DB, sql.c_str(), callback, NULL, NULL);

	if (rc != SQLITE_OK) {
		std::cerr << "Error SELECT" << std::endl;
		return (-1);
	}
	sqlite3_close(DB);	
	return 0;
}

int main(int argc, char** argv) {
	RC522_setup(7);
	PcdReset ();
	M500PcdConfigISOType('A');
	
	bool create_wav = true;
	if(argc==2) {
		if(strcmp(argv[1],"-otf")==0) {
			create_wav = false;
		}
		else {
			fprintf(stderr,"usage: either no params or use the switch \n-otf \t\t Play the TTS on the fly instead of creating a .wav file.\n");
			return 1;
		}
	}
	
	std::string welcome_msg = "Please scan a tag.";
	create_tts(welcome_msg, create_wav, "please_scan_a_tag");
	std::cout << welcome_msg << std::endl;
	while(1) {
		std::string nfcID = "";
		nfcID = get_nfc_ID();
		if(nfcID.length() > 0) {
			std::cout << "Tag successfully scanned: " << nfcID << " ... searching for matching record..." << std::endl;
			get_desc_from_db(nfcID);
			std::string scan_another_msg = "Please scan another tag.";
			create_tts(scan_another_msg, create_wav, "please_scan_another_tag");
			std::cout << scan_another_msg << std::endl;
		}
	}

	return (0);
}

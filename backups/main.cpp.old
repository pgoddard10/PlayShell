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
	std::cout << "\ta) Creating some TTS" << std::endl;
	std::string str = "flite -voice cmu_us_slt"; //change from default voice
	if(as_file == true) {
		str = str + " -o " + filename + ".wav"; //save as file (instead of reading aloud)
	}
	str = str + " -t '" + text + "' --setf duration_stretch=1.25"; //specify the text and slow the voice down
	std::cout << "\tb) Created string: " << str << std::endl;
	const char *command = str.c_str(); //convert the string into a char array
	std::cout << "\tc) string created" << std::endl;
	system(command); //run as a system command
	std::cout << "\td) TTS finished (processed). Pausing..." << std::endl;
	delay(200); //pause to ensure speech has finished
}

static int callback(void* data, int argc, char** argv, char** azColName) {
	int i;
	//fprintf(stderr, "%s: ", (const char*)data);
	std::string tagID = "";
	for (i = 0;i < argc;i++) {
		//printf("%s = %s\n", azColName[i], argv[i] ? argv[i] : "NULL");
		//printf("this: %s",argv[i]);
		std::cout << "Column Name: " << azColName[i] << std::endl;
		std::cout << "Contents: " << argv[i] <<std::endl;
		if(strcmp(azColName[i],"id")==0) {
			tagID = argv[i];
		}
		else {
			create_tts(argv[i], true, tagID);
			std::cout << "Pausing again, just incase..." << std::endl;
			delay(200); //pause to ensure speech has finished
		}
	}
	return 0;
}

std::string get_nfc_ID(){
	char cStr [ 30 ];
	uint8_t ucArray_ID [ 4 ];    /*先后存放IC卡的类型和UID(IC卡序列号)*/
	uint8_t ucStatusReturn;      /*返回状态*/
	//static uint8_t ucLineCount = 0;
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

				//not needed anymore
				// ucLineCount ++;
				// if ( ucLineCount == 17 ) {
					// ucLineCount = 0;
				// }
				// return cStr;
			}
		}
	}
}

int main(int argc, char** argv) {
	sqlite3* DB;
	//char* messaggeError;
	//int exit = sqlite3_open("audio_culture.db", &DB);
	//std::string query = "SELECT * FROM tag;";

	//std::cout << "STATE OF TABLE BEFORE INSERT" << std::endl;

	//sqlite3_exec(DB, query.c_str(), callback, NULL, NULL);

	RC522_setup(7);
	PcdReset ();
	M500PcdConfigISOType('A');
	//delay(100); //to let the program wake up properly before trying to trigger TTS
	std::string welcome_msg = "Please scan a tag.";
	create_tts(welcome_msg, false);
	std::cout << welcome_msg << std::endl;
	while(1) {
		std::string nfcID = "";
		nfcID = get_nfc_ID();
		if(nfcID.length() > 0) {
			std::cout << "Tag successfully scanned: " << nfcID << " ... searching for matching record..." << std::endl;

			int exit = sqlite3_open("audio_culture.db", &DB);

			//select tag from database:
			std::string sql("SELECT id, desc FROM tag WHERE ID = '"+nfcID+"';");
			if (exit) {
				std::cerr << "Error open DB " << sqlite3_errmsg(DB) << std::endl;
				return (-1);
			}
			// else
				// std::cout << "Opened Database Successfully!" << std::endl;

			int rc = sqlite3_exec(DB, sql.c_str(), callback, NULL, NULL);

			if (rc != SQLITE_OK)
				std::cerr << "Error SELECT" << std::endl;
			// else {
				// std::cout << "Query successful (not that the results are positive)" << std::endl;
			// }


			////insert tag ID into database:
			// std::string sql_ins("INSERT INTO tag VALUES('"+nfcID+"','desc');");

			// exit = sqlite3_exec(DB, sql_ins.c_str(), NULL, 0, &messaggeError);
			// if (exit != SQLITE_OK) {
				// std::cerr << "Error Insert" << std::endl;
				// sqlite3_free(messaggeError);
			// }
			// else
				// std::cout << "Records created Successfully!" << std::endl;

			// std::cout << "STATE OF TABLE AFTER INSERT" << std::endl;

			// sqlite3_exec(DB, query.c_str(), callback, NULL, NULL);
			sqlite3_close(DB);
			std::string scan_another_msg = "Please scan another tag.";
			create_tts(scan_another_msg, false);
			std::cout << scan_another_msg << std::endl;
		}
		delay(100);
	}

	//sqlite3_close(DB);
	return (0);
}

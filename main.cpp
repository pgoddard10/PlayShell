#include <stdio.h>
#include <stdint.h>
#include <stdbool.h>
#include <string.h>
#include <iostream>

#include <sqlite3.h>
#include <wiringPi.h>
#include <wiringPiSPI.h>

#include "RC522.c"

static int callback(void* data, int argc, char** argv, char** azColName) {
	int i;
	//fprintf(stderr, "%s: ", (const char*)data);

	for (i = 0;i < argc;i++) {
		printf("%s = %s\n", azColName[i], argv[i] ? argv[i] : "NULL");
		printf("this: %s",argv[i]);
		std::string str = "flite -voice cmu_us_slt -t '";
		str = str + argv[i] + "'"; 
		const char *command = str.c_str(); 
		system(command);
		delay(100);
	}

	printf("\n");
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
	int exit = sqlite3_open("audio_culture.db", &DB);
	std::string query = "SELECT * FROM tag;";

	//std::cout << "STATE OF TABLE BEFORE INSERT" << std::endl;

	//sqlite3_exec(DB, query.c_str(), callback, NULL, NULL);

	RC522_setup(7);
	PcdReset ();
	M500PcdConfigISOType('A');
	printf("Start NFC scan\r\n");
	std::string str = "flite -voice cmu_us_slt -t ";
	str = str + "'Please scan a tag.'"; 
	const char *command = str.c_str(); 
	system(command);
	delay(100);
	while(1) {
		std::string nfcID = "";
		nfcID = get_nfc_ID();
		if(nfcID.length() > 0) {
			std::cout << "Tag successfully scanned: " << nfcID << " ... searching for matching record..." << std::endl;

			//insert tag ID into database:
			std::string sql("SELECT desc FROM tag WHERE ID = '"+nfcID+"';");
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
			
		}
		delay(100);
	}

	sqlite3_close(DB);
	return (0);
}

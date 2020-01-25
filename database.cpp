/**
 *  @Author: Paul Goddard - 17019749
 *  @Date: Winter/Spring 2019-2020
 *  @Description: 
 *
 */
 
#include "database.h"

/** constructor, as per default settings */
Database::Database() {
}

/** deconstructor, as per default settings */
Database::~Database() {
}

//was static int
int Database::callback(void* data, int argc, char** argv, char** azColName) {
	int i;
	std::string tagID = "";
	for (i = 0;i < argc;i++) {
		if(strcmp(azColName[i],"id")==0) {
			tagID = argv[i];
		}
		else {
			std::cout << "Record found: \n" << argv[i] << std::endl; 
			//create_tts(argv[i], true, tagID);
		}
	}
	return 0;
}


int Database::get_desc_from_db(std::string nfcID) {
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
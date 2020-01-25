/**
 *  @Author: Paul Goddard - 17019749
 *  @Date: Winter/Spring 2019-2020
 *  @Description: 
 *
 */
 
#include "database.h"

/** constructor, as per default settings */
Database::Database() {
	db_name = "audio_culture.db";
}

/** deconstructor, as per default settings */
Database::~Database() {
}

std::string Database::get_tag_desc(std::string nfcID) {
	sqlite3* db;
	int exit = sqlite3_open(this->db_name, &db);
	if (exit) {
		std::cerr << "Error opening db " << sqlite3_errmsg(db) << std::endl;
		return "";
	}
	
	std::string str = "SELECT id, desc FROM tag WHERE ID = '"+nfcID+"';";
	const char* search_str = str.c_str();
	
						std::string desc;
	
	sqlite3_stmt *statement;

	if(sqlite3_prepare_v2(db, search_str, -1, &statement, 0) == SQLITE_OK)
	{
		int cols = sqlite3_column_count(statement);
		int result = 0;
		while(true)
		{
			result = sqlite3_step(statement);
			
			if(result == SQLITE_ROW)
			{
				for(int col = 0; col < cols; col++)
				{
					std::string s = (char*)sqlite3_column_text(statement, col);
					if(col==0) {
						std::cout << "Tag ID found: " << s << std::endl;
					}
					else if(col==1) {
						desc = s;
					}
				}
			}
			else
			{
				break;   
			}
		}
	   
		sqlite3_finalize(statement);
	}
	
	sqlite3_close(db);
	
	return desc;
}
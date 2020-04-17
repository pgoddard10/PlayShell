/**
 *  @Author: Paul Goddard - 17019749
 *  @Date: Winter/Spring 2019-2020
 *  @Description: 
 *
 *
 * Database functions built based on https://www.dreamincode.net/forums/topic/122300-sqlite-in-c/ [25/01/2020]
 */
 
#include "database.h"

/** constructor, as per default settings */
Database::Database(const char* db_name) {
	this->db_name = db_name;
	this->db = NULL;
}

/** deconstructor, as per default settings */
Database::~Database() {
}

bool Database::open() {
	if(sqlite3_open(this->db_name, &db) == SQLITE_OK) {
		return true;
	}
	return false;
}

void Database::close() {
	sqlite3_close(this->db);
}

int Database::query(std::string query) {
	if(this->open()) {
		const char* search_str = query.c_str();
		
		sqlite3_stmt *statement;
		this->results.clear();

		if(sqlite3_prepare_v2(this->db, search_str, -1, &statement, 0) == SQLITE_OK) {
			int cols = sqlite3_column_count(statement);
			int result = 0;
			while(true) {
				result = sqlite3_step(statement);
				
				if(result == SQLITE_ROW) {
					std::vector<std::string> values;
					for(int col = 0; col < cols; col++) {
						values.push_back((char*)sqlite3_column_text(statement, col));
					}
					this->results.push_back(values);
				}
				else {
					break;   
				}
			}
		   
			//sqlite3_exec(this->db, "COMMIT;", NULL, NULL, NULL);
			sqlite3_finalize(statement);
		}
		
		std::string error = sqlite3_errmsg(this->db);
		this->close();
		
		if(error != "not an error") {
			std::cout << query << " " << error << std::endl;
			return -1;
		}
		return 0;
	}
	else {
		return -1;
	}
}


bool Database::does_tag_exist(std::string nfcID){
	std::string str = "SELECT ID FROM tag WHERE ID = '"+nfcID+"';"; //build query
	this->query(str); //run query in database

	if(this->results.empty()) {
		return false;
	}
	return true;
}

std::string Database::get_tag_desc(std::string nfcID) {
	std::string str = "SELECT desc FROM tag WHERE ID = '"+nfcID+"';"; //build query
	this->query(str); //run query in database
	return this->results[0][0]; //return description
}

int Database::save_desc(std::string nfcID, std::string desc) {
	std::string str = "INSERT into tag VALUES('"+nfcID+"','"+desc+"');"; //build query
	if(this->query(str)==0) { //run query in database
		return 0;
	}
	return -1;
}

int Database::delete_tag(std::string nfcID) {
	std::string str = "DELETE FROM tag WHERE ID = '"+nfcID+"';"; //build query
	if(this->query(str)==0) { //run query in database
		return 0;
	}
	return -1;
}
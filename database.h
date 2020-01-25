/**
 *  @Author: Paul Goddard - 17019749
 *  @Date: Winter/Spring 2019-2020
 *  @Description: 
 *
 */

#ifndef DATABASE_H
#define DATABASE_H

#include <iostream>
#include <string.h>
#include <vector>
#include <sqlite3.h> //for the database interaction

class Database {

	public:
		Database(const char* db_name); /** constructor, as per default settings */
		~Database(); /** deconstructor, as per default settings */
		bool does_tag_exist(std::string nfcID);
		std::string get_tag_desc(std::string);

	private:
		const char* db_name; //name of the database
		sqlite3* db; //database object
		std::vector<std::vector<std::string>> results; //something to store the query results in
		
		bool open();
		void close();
		int query(std::string nfcID);
		 
};

#endif // DATABASE_H

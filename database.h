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
#include <sqlite3.h> //for the database interaction

class Database {

	public:
		Database(); /** constructor, as per default settings */
		~Database(); /** deconstructor, as per default settings */
		std::string get_tag_desc(std::string nfcID);

	private:
		const char* db_name; //name of the database
		 
};

#endif // DATABASE_H

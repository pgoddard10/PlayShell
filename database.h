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
	int get_desc_from_db(std::string nfcID);

 private:
	static int callback(void* data, int argc, char** argv, char** azColName);
 
};

#endif // DATABASE_H

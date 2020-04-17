#include "Content_Model.h"

Content_Model::Content_Model() {
}

Content_Model::~Content_Model() {
}


std::string Content_Model::get_tag_id() {
    return this->tag_id;
}

int Content_Model::get_item_id() {
    return this->item_id;
}

int Content_Model::get_content_id() {
    return this->content_id;
}

std::vector<int> Content_Model::get_all_ids_from_db() {
    const char* db_name = "audio_culture.db";
    sqlite3* db_obj; //database object
    std::vector<int> vec_content_ids;

	if(sqlite3_open(db_name, &db_obj) == SQLITE_OK) { //open db
		const char* search_str = "SELECT content_id FROM content LEFT JOIN item ON content.item_id = item.item_id WHERE content.active = 1 AND item.active = 1";
		
		sqlite3_stmt *statement;

		if(sqlite3_prepare_v2(db_obj, search_str, -1, &statement, 0) == SQLITE_OK) {
			int result = 0;
			while(true) {
				result = sqlite3_step(statement);
				
				if(result == SQLITE_ROW) {
                    int content_id = (int)sqlite3_column_int(statement, 0);
                    vec_content_ids.push_back(content_id);
                    // std::cout << "Found a content ID in the db: " << content_id <<std::endl;
				}
				else {
					break;   
				}
			}
			sqlite3_finalize(statement);
		}
		
		std::string error = sqlite3_errmsg(db_obj);
		sqlite3_close(db_obj);
		
        return vec_content_ids;
	}
    return vec_content_ids;
}


// based heavily on code from https://stackoverflow.com/a/31747742/2747620
int Content_Model::populate_from_db(int content_id) {
    const char* db_name = "audio_culture.db";
    sqlite3* conn;
    sqlite3_stmt* stmt = 0;

    int rc = sqlite3_open(db_name, &conn);
    //  Good idea to always check the return value of sqlite3 function calls. 
    //  Only done once in this example:
    if ( rc != SQLITE_OK ) {
        return -1;
    }
    else {
        rc = sqlite3_prepare_v2( conn, "SELECT content_id, tag_id, next_content, gesture_id, content.item_id FROM content LEFT JOIN item ON content.item_id = item.item_id WHERE content.active = 1 AND item.active = 1 AND content_id = ?", -1, &stmt, 0 );

        //  Optional, but will most likely increase performance.
        rc = sqlite3_exec( conn, "BEGIN TRANSACTION", 0, 0, 0 );    

        rc = sqlite3_bind_int( stmt, 1, content_id ); // Bind  parameter.

        while ( sqlite3_step( stmt ) == SQLITE_ROW ) { // While query has result-rows.
        
            this->content_id = (int)sqlite3_column_int(stmt, 0); 
            const char *columnText = (const char *)sqlite3_column_text(stmt, 1);
            if(columnText != NULL) {
                this->tag_id = columnText;
            }
            this->next_content = (int)sqlite3_column_int(stmt, 2);
            this->gesture_id = (int)sqlite3_column_int(stmt, 3);
            this->item_id = (int)sqlite3_column_int(stmt, 4);
        }
        char *zErrMsg = 0;  //  Can perhaps display the error message if rc != SQLITE_OK.
        rc = sqlite3_exec( conn, "END TRANSACTION", 0, 0, &zErrMsg );   //  End the transaction.

        rc = sqlite3_finalize( stmt );  //  Finalize the prepared statement.
    }
    return 0;
}

Json::Value Content_Model::read_new_content_json() {
    //read the JSON file and get the content ID
    std::cout << this->new_content_json << std::endl;
    std::ifstream ifs_json(this->new_content_json);
    Json::Value obj;
    if(ifs_json.is_open()) { //only continue if the file is found
        Json::Reader reader;

        bool parsingSuccessful = reader.parse(ifs_json, obj);
        if ( !parsingSuccessful ) {
            std::cout << "Error parsing the string" << std::endl;
        }


        //clear out the database so it's ready for the new data
        DELETE FROM content
        DELETE FROM item



        const Json::Value data = obj["data"]; //grab the top level from the JSON file

        for (uint i = 0; i < data.size(); ++i ){ //start looping through the top levels (i.e. the Items)
            for(uint j = 0; j < data[i]["content"].size(); j++) { //loop through the second level (i.e. the Content)

                //set some temporary variables
                int item_active;
                int content_active;
                int gesture;
                int content_id;
                int item_id;
                int next_content;
                std::string tag_id;

                //get the data from the JSON file and correctly convert into the relevant data types
                item_id = data[i]["item_id"].asInt();
                content_id = stoi(data[i]["content"][j]["content_id"].asString());
                if(!data[i]["content"][j]["gesture_id"].asString().empty()) {
                    gesture = stoi(data[i]["content"][j]["gesture_id"].asString());
                }
                if(!data[i]["content"][j]["next_content_id"].asString().empty()) {
                    next_content = stoi(data[i]["content"][j]["next_content_id"].asString());
                }
                tag_id = data[i]["content"][j]["tag_id"].asString();
                content_active = data[i]["content"][j]["active"].asInt();
                if((data[i]["active"].asString().compare("Yes")) == 0){ //convert the Yes/No into 1/0
                    item_active = 1;
                }
                else {
                    item_active = 0;
                }

                //only save content if it's active, otherwise it's a waste of resources
                if(item_active && content_active) {
                    //commit to database
                }
            }
        }
        
        ifs_json.close(); //close the file handler
    }
    return obj;
}
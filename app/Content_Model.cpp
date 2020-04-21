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

int Content_Model::get_current_status() {
    int status = -1;
    //read the JSON file and get the content ID
    std::ifstream ifs_status_json(this->status_json);
    if(ifs_status_json.is_open()) { //only continue if the file is found
        Json::Reader reader;
        Json::Value obj;
        reader.parse(ifs_status_json, obj);
        status = obj["status"]["code"].asInt();
        ifs_status_json.close(); //close the file handler
    }
    return status;
}

int Content_Model::get_current_visitor() {
    int visitor_id = -1;
    //read the JSON file and get the content ID
    std::ifstream ifs(this->incoming_visitor_json);
    if(ifs.is_open()) { //only continue if the file is found
        Json::Reader reader;
        Json::Value obj;
        reader.parse(ifs, obj);
        visitor_id = obj["data"]["visitor_id"].asInt();
        ifs.close(); //close the file handler
    }
    return visitor_id;
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
			while(true) { //loop through all results
				result = sqlite3_step(statement);
				
				if(result == SQLITE_ROW) {
                    //get the content_id from this loop
                    int content_id = (int)sqlite3_column_int(statement, 0);
                    vec_content_ids.push_back(content_id); //add the ID to the vector
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
        sqlite3_free(zErrMsg);
    }
    return 0;
}

int Content_Model::save_new_content_json() {
    //read the JSON file and get the content ID
    std::ifstream ifs_json(this->new_content_json);
    Json::Value obj;
    if(ifs_json.is_open()) { //only continue if the file is found
        Json::Reader reader;

        bool parsingSuccessful = reader.parse(ifs_json, obj);
        if ( !parsingSuccessful ) {
            std::cout << "Error parsing the string" << std::endl;
        }

        //clear out the database so it's ready for the new data
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
            char *zErrMsg = 0;  //  Can perhaps display the error message if rc != SQLITE_OK.

            //  Optional, but will most likely increase performance.
            rc = sqlite3_exec( conn, "BEGIN TRANSACTION", 0, 0, 0 ); 
            
            rc = sqlite3_prepare_v2( conn, "DELETE FROM content", -1, &stmt, 0 );
            sqlite3_step( stmt ); //executing the DELETE FROM content statement
            
            rc = sqlite3_prepare_v2( conn, "DELETE FROM item", -1, &stmt, 0 );
            sqlite3_step( stmt ); //executing the DELETE FROM item statement
            
            const Json::Value data = obj["data"]; //grab the top level from the JSON file

            for (uint i = 0; i < data.size(); ++i ){ //start looping through the top levels (i.e. the Items)
            
                //set some temporary variables
                int item_active;
                int item_id;
                std::string item_name;
                item_id = data[i]["item_id"].asInt();
                item_name = data[i]["name_without_url"].asString(); 

                if((data[i]["active"].asString().compare("Yes")) == 0){ //convert the Yes/No into 1/0
                    item_active = 1;
                }
                else {
                    item_active = 0;
                }

                if(item_active) {
                    //commit to database
                    rc = sqlite3_prepare_v2( conn, "INSERT INTO item (item_id, name, active) VALUES (?,?,1)", -1, &stmt, 0 );
                    rc = sqlite3_bind_int( stmt, 1, item_id ); // Bind  parameter.
                    rc = sqlite3_bind_text( stmt, 2, item_name.c_str(), item_name.length(), NULL ); // Bind  parameter.
                    sqlite3_step( stmt ); //executing the INSERT INTO item statement
                }
                 
                for(uint j = 0; j < data[i]["content"].size(); j++) { //loop through the second level (i.e. the Content)

                    //set some temporary variables
                    int content_active;
                    std::string content_name;
                    int gesture_id = 0;
                    int content_id;
                    int next_content = 0;
                    std::string tag_id;

                    //get the data from the JSON file and correctly convert into the relevant data types            
                    content_id = stoi(data[i]["content"][j]["content_id"].asString()); 
                    content_name = data[i]["content"][j]["name"].asString();
                    if(!data[i]["content"][j]["gesture_id"].asString().empty()) {
                        gesture_id = stoi(data[i]["content"][j]["gesture_id"].asString());
                    }
                    if(!data[i]["content"][j]["next_content_id"].asString().empty()) {
                        next_content = stoi(data[i]["content"][j]["next_content_id"].asString());
                    }
                    tag_id = data[i]["content"][j]["tag_id"].asString();
                    content_active = data[i]["content"][j]["active"].asInt();

                    //only save content if it's active, otherwise it's a waste of resources
                    if(item_active && content_active) {
                        //commit to database
                        rc = sqlite3_prepare_v2( conn, "INSERT INTO content (content_id, name, tag_id, tts_enabled, next_content, active, gesture_id, item_id) VALUES (?,?,?,0,?,1,?,?)", -1, &stmt, 0 );
                        rc = sqlite3_bind_int( stmt, 1, content_id ); // Bind  parameter.
                        rc = sqlite3_bind_text( stmt, 2, content_name.c_str(), content_name.length(), NULL  ); // Bind  parameter.
                        rc = sqlite3_bind_text( stmt, 3, tag_id.c_str(), tag_id.length(), NULL  ); // Bind  parameter.
                        rc = sqlite3_bind_int( stmt, 4, next_content ); // Bind  parameter.
                        rc = sqlite3_bind_int( stmt, 5, gesture_id ); // Bind  parameter.
                        rc = sqlite3_bind_int( stmt, 6, item_id ); // Bind  parameter.
                        sqlite3_step( stmt ); //executing the INSERT INTO content statement
                    }
                }
            }
            
            ifs_json.close(); //close the file handler

            rc = sqlite3_exec( conn, "END TRANSACTION", 0, 0, &zErrMsg );   //  End the transaction.
            rc = sqlite3_finalize( stmt );  //  Finalize the prepared statement.
            sqlite3_free(zErrMsg);

            //now that the update has finished, set the device status back to "ready" (code 0)
            //if the device is ready, it means that the CMS can start another action
            Json::Value root;
            root["status"]["code"] = 0;
            root["status"]["name"] = "Device is ready";

            Json::FastWriter writer;
            const std::string json_file = writer.write(root);

            std::ofstream ifs(this->status_json); //open the file to respond to the request
            if (ifs.is_open()) {
                ifs << json_file; //push the contents of the json_file into the actual file
                ifs.close(); //close the file handler
                std::cout << "Saved the JSON formatted content: " << json_file << std::endl;
            }

        }
        
    }
    return 0;
}
/**
 * Class Content_Model
 * Responsible for handling database and JSON data/interaction
 *
 * @author	Paul Goddard
 * 			paul2.goddard@live.uwe.ac.uk
 * 			https://github.com/pgoddard10/
 * 			https://www.linkedin.com/in/pgoddard10/
 * 			https://twitter.com/pgoddard10
 * @date Spring 2020 
 */

#include "Content_Model.h"

/**
 * method Content_Model()
 * default constructor
 */
Content_Model::Content_Model() {
    this->new_content_json = "cms_data_exchange/published_content.json";
    this->status_json = "cms_data_exchange/status.json";
    this->db_name = "audio_culture.db";
}

/**
 * method ~Content_Model()
 * default desstructor
 */
Content_Model::~Content_Model() {
}

/**
 * method get_tag_id()
 * @return std::string tag_id - the ID of the NFC tag stored in the Model (ultimately from the database)
 */
std::string Content_Model::get_tag_id() {
    return this->tag_id;
}

/**
 * method get_item_id()
 * @return int item_id - the ID of the item stored in the Model (ultimately from the database)
 */
int Content_Model::get_item_id() {
    return this->item_id;
}

/**
 * method get_content_id()
 * @param  std::string 
 * @return int content_id - the ID of the content stored in the Model (ultimately from the database)
 */
int Content_Model::get_content_id() {
    return this->content_id;
}

/**
 * method get_gesture_id()
 * @param  std::string 
 * @return int gesture_id - the ID of the gesture stored in the Model (ultimately from the database)
 */
int Content_Model::get_gesture_id() {
    return this->gesture_id;
}

/**
 * method get_next_content()
 * @return int next_content - the ID of the next_content (to be played) stored in the Model (ultimately from the database)
 */
int Content_Model::get_next_content() {
    return this->next_content;
}

/**
 * method get_current_status()
 * Gets the current device status from the status JSON
 * @param  std::string 
 * @return int 
 */
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

/**
 * method get_all_ids_from_db()
 * Gets all of the content IDs from the database. This is used to populate a vector of models
 * @return vector vec_content_ids A vector of content_id s
 */
std::vector<int> Content_Model::get_all_ids_from_db() {
    sqlite3* db_obj; //database object
    std::vector<int> vec_content_ids;

	if(sqlite3_open(this->db_name, &db_obj) == SQLITE_OK) { //open db
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
	}
    return vec_content_ids;
}

/**
 * method update_device_status()
 * Overwrites the status JSON file with the status number passed to this method
 */
void Content_Model::update_device_status(int status) {
    std::string name;
    if(status==0) name = "Device is ready";
    else if(status==1) name = "Device is in use";
    else if(status==2) name = "Device is updating";
    else if(status==3) name = "CMS is updating";

    Json::Value root;
    root["status"]["code"] = status;
    root["status"]["name"] = name;

    Json::FastWriter writer;
    const std::string json_file = writer.write(root);

    std::ofstream ifs(this->status_json); //open the file to respond to the request
    if (ifs.is_open()) {
        ifs << json_file; //push the contents of the json_file into the actual file
        ifs.close(); //close the file handler
    }
}

/**
 * method populate_from_db()
 * populates this model with the data from the datbase. One model = one content row from the db 
 *  based heavily on code from https://stackoverflow.com/a/31747742/2747620
 * @param int content_id 
 * @return int
 */
int Content_Model::populate_from_db(int content_id) {
    sqlite3* conn;
    sqlite3_stmt* stmt = 0;

    int rc = sqlite3_open(this->db_name, &conn);
    //  Good idea to always check the return value of sqlite3 function calls. 
    //  Only done once in this example:
    if ( rc != SQLITE_OK ) {
        return -1;
    }
    else {
        rc = sqlite3_prepare_v2( conn, "SELECT content_id, tag_id, next_content, gesture_id, content.item_id FROM content LEFT JOIN item ON content.item_id = item.item_id WHERE content.active = 1 AND item.active = 1 AND content_id = ?", -1, &stmt, 0 );

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

/**
 * method save_new_content_from_json()
 * opens the published_content JSON file and replaces the data in the database with the contents of the file
 * @return int
 */
int Content_Model::save_new_content_from_json() {
    //read the JSON file and get the content ID
    std::ifstream ifs_json(this->new_content_json);
    Json::Value obj;
    if(ifs_json.is_open()) { //only continue if the file is found
        Json::Reader reader;

        bool parsingSuccessful = reader.parse(ifs_json, obj);
        if ( !parsingSuccessful ) {
            return -1;
        }

        //clear out the database so it's ready for the new data
        sqlite3* conn;
        sqlite3_stmt* stmt = 0;

        int rc = sqlite3_open(this->db_name, &conn);
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
            this->update_device_status(0);
        }
        
    }
    return 0;
}


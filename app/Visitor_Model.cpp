#include "Visitor_Model.h"

Visitor_Model::Visitor_Model() {
    this->set_current_visitor();
}

Visitor_Model::~Visitor_Model() {
}


int Visitor_Model::save_visitor_details_as_json() {
    
    sqlite3* conn;
    sqlite3_stmt* stmt = 0;

    int rc = sqlite3_open(this->db_name, &conn);
    //  Good idea to always check the return value of sqlite3 function calls. 
    //  Only done once in this example:
    if ( rc != SQLITE_OK ) {
        return -1;
    }
    else {
        rc = sqlite3_prepare_v2( conn, "SELECT content_id, time_scanned, visitor_id FROM visitor_history", -1, &stmt, 0 );

        rc = sqlite3_exec( conn, "BEGIN TRANSACTION", 0, 0, 0 );    

        Json::Value root;
        int row = 0;

        while ( sqlite3_step( stmt ) == SQLITE_ROW ) { // While query has result-rows.
        
            int content_id = (int)sqlite3_column_int(stmt, 0); 
            const char *time_scanned = (const char *)sqlite3_column_text(stmt, 1);
            int visitor_id = (int)sqlite3_column_int(stmt, 2); 

            
            root["data"][row]["content_id"] = content_id;
            root["data"][row]["time_scanned"] = time_scanned;
            root["data"][row]["visitor_id"] = visitor_id;
            row++;

        }
        char *zErrMsg = 0;
        rc = sqlite3_exec( conn, "END TRANSACTION", 0, 0, &zErrMsg );   //  End the transaction.

        rc = sqlite3_finalize( stmt );  //  Finalize the prepared statement.
        sqlite3_free(zErrMsg);

        
        Json::FastWriter writer;
        const std::string json_file = writer.write(root);

        std::ofstream ifs(this->outgoing_visitor_data); //open the file to respond to the request
        if (ifs.is_open()) {
            ifs << json_file; //push the contents of the json_file into the actual file
            ifs.close(); //close the file handler
        }

    }

    return 0;
}

int Visitor_Model::set_current_visitor() {
    //read the JSON file and get the content ID
    std::ifstream ifs(this->incoming_visitor_json);
    if(ifs.is_open()) { //only continue if the file is found
        Json::Reader reader;
        Json::Value obj;
        reader.parse(ifs, obj);
        this->visitor_id = obj["data"]["visitor_id"].asInt();
        ifs.close(); //close the file handler
    }
    return this->visitor_id;
}

/**
 * method save_visitor_interaction()
 * Saves the visitor_id and content_id into the db with a timestamp.
 * This data is used for reporting and bookmarking purposes.
 * @param  int content_id - contains the ID number of the content to insert into the db
 * @return int 
 */
int Visitor_Model::save_visitor_interaction(int content_id) {

    sqlite3* conn;
    sqlite3_stmt* stmt = 0;

    int rc = sqlite3_open(this->db_name, &conn);
    //  Check the return value of sqlite3 function calls. 
    if ( rc != SQLITE_OK ) {
        return -1;
    }
    else {
        char *zErrMsg = 0; 

        rc = sqlite3_exec( conn, "BEGIN TRANSACTION", 0, 0, 0 ); 
        
        //commit to database
        rc = sqlite3_prepare_v2( conn, "INSERT INTO visitor_history (content_id, visitor_id) VALUES (?,?)", -1, &stmt, 0 );
        rc = sqlite3_bind_int( stmt, 1, content_id ); // Bind  parameter.
        rc = sqlite3_bind_int( stmt, 2, this->visitor_id ); // Bind  parameter.
        sqlite3_step( stmt ); //executing the INSERT INTO item statement

        rc = sqlite3_exec( conn, "END TRANSACTION", 0, 0, &zErrMsg );   //  End the transaction.
        rc = sqlite3_finalize( stmt );  //  Finalize the prepared statement.
        sqlite3_free(zErrMsg);
    }
    return 0;
}
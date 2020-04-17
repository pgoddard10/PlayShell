#include "Content_Model.h"


Content_Model::Content_Model() {
}

Content_Model::~Content_Model() {
}


Json::Value Content_Model::read_new_content_json() {

    std::cout << "hello - this is Json::Value Content_Model::read_new_content_json()" << std::endl;

    //read the JSON file and get the content ID
    std::cout << this->new_content_json << std::endl;
    std::ifstream ifs_json("cms_data_exchange/published_content.json");
    Json::Value obj;
    if(ifs_json.is_open()) { //only continue if the file is found
        Json::Reader reader;
        reader.parse(ifs_json, obj);


        // for (Json::Value::ArrayIndex i = 0; i != obj.size(); i++)
        //     if (obj[i].isMember("attr1"))
        //         values.push_back(obj[i]["attr1"].asString());

        
        // current_status = obj["status"]["code"].asInt();


        ifs_json.close(); //close the file handler
    }
    return obj;
}



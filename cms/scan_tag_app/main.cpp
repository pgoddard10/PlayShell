#include <stdio.h>
#include <stdint.h>
#include <stdbool.h>
#include <string.h>
#include <iostream>
#include <fstream>
#include <jsoncpp/json/json.h>
#include "nfc.h" //NFC library for NFC tag interaction

int main(int argc, char** argv) {
	NFC nfc = NFC();

	//never stop!
	while(1) {
		std::cout << "Ready! Please scan a tag..." << std::endl;
		std::string nfcID = "";
		nfcID = nfc.get_nfc_ID(); //wait here until a tag is scanned
		std::cout << "Tag successfully scanned. The ID is: " << nfcID << std::endl;

		//read the JSON file and get the content ID
		std::ifstream ifs("../json/tag_setup/content.json");
		if(ifs.is_open()) { //only continue if the file is found
			Json::Reader reader;
			Json::Value obj;
			reader.parse(ifs, obj);
			std::string content_id = obj["content_id"].asString();
			ifs.close(); //close the file handler
			std::cout << "File opened and read. The Content ID is: " << content_id << std::endl;

			if(content_id.length()>0) { //if the content_id actually exists
				//build a JSON tree with the content_id and NFC tag ID
				Json::Value root;
				root["content_id"] = content_id;
				root["nfc_tag"] = nfcID;

				Json::FastWriter writer;
				const std::string json_file = writer.write(root);

 				//open the file to respond to the request
				std::ofstream results_file("../json/tag_setup/tag_data.json");
				if (results_file.is_open()) {
					results_file << json_file; //push the contents of the json_file into the actual file
					results_file.close(); //close the file handler
					std::cout << "Saved the JSON formatted content: " << json_file << std::endl;
				}
			}
		}
	}
	return (0);
}

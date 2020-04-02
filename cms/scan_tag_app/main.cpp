#include <stdio.h>
#include <stdint.h>
#include <stdbool.h>
#include <string.h>
#include <iostream>
#include <fstream>
#include <jsoncpp/json/json.h>

#include "nfc.h" //for NFC tag interaction

int main(int argc, char** argv) {
	NFC nfc = NFC();

	while(1) {
		std::cout << "Ready! Please scan a tag..." << std::endl;
		std::string nfcID = "";
		nfcID = nfc.get_nfc_ID(); //wait here until a tag is scanned
		std::cout << "Tag successfully scanned. The ID is: " << nfcID << std::endl;

		//read the JSON file and get the content ID
		std::ifstream ifs("../json/content.json");
		if(ifs.is_open()) { //only continue if the file is found
			Json::Reader reader;
			Json::Value obj;
			reader.parse(ifs, obj);
			std::string content_id = obj["content_id"].asString();
			ifs.close(); //close the file handler
			std::cout << "File opened and read. The Content ID is: " << content_id << std::endl;

			if(content_id.length()>0) {

				Json::Value root;
				root["content_id"] = content_id;
				root["nfc_tag"] = nfcID;

				Json::FastWriter writer;
				const std::string json_file = writer.write(root);

				std::ofstream results_file("../json/tag_data.json"); //open the file to respond to the request
				if (results_file.is_open()) {
					results_file << json_file;
					results_file.close(); //close the file handler
					std::cout << "Saved the JSON formatted content: " << json_file << std::endl;
				}
			}
		}
	}
	return (0);
}

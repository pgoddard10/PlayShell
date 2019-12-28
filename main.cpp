#include <wiringPi.h>
#include <wiringPiSPI.h>
#include <stdio.h>
#include <stdint.h>
#include <stdbool.h>
#include <string.h>

#include <iostream>

#include "RC522.c"

std::string get_nfc_ID(){
	char cStr [ 30 ];
	uint8_t ucArray_ID [ 4 ];    /*先后存放IC卡的类型和UID(IC卡序列号)*/
	uint8_t ucStatusReturn;      /*返回状态*/
	static uint8_t ucLineCount = 0;
	while ( 1 ) {
//		printf("while(1)");
		/*寻卡*/
		if ( ( ucStatusReturn = PcdRequest ( PICC_REQALL, ucArray_ID ) ) != MI_OK ) { /*若失败再次寻卡*/
			ucStatusReturn = PcdRequest ( PICC_REQALL, ucArray_ID );
		}

		if ( ucStatusReturn == MI_OK  ) {
//			printf("OK");
			/*防冲撞（当有多张卡进入读写器操作范围时，防冲突机制会从其中选择一张进行操作）*/
			if ( PcdAnticoll ( ucArray_ID ) == MI_OK ) {
//				printf("card");
				sprintf ( cStr, "The Card ID is: %02X%02X%02X%02X",
				          ucArray_ID [ 0 ],
				          ucArray_ID [ 1 ],
				          ucArray_ID [ 2 ],
				          ucArray_ID [ 3 ] );
//				printf ( "%s\r\n", cStr );
				return cStr;

				//not needed anymore
				ucLineCount ++;
				if ( ucLineCount == 17 ) {
					ucLineCount = 0;
				}
				return cStr;
			}
		}
	}
}

int main() {
	RC522_setup(7);
	PcdReset ();
	M500PcdConfigISOType('A');
	printf ( "Start NFC scan\r\n" );
	while(1) {
		std::string nfcID = "";
		nfcID = get_nfc_ID();
		std::cout << "length: " << nfcID.length() << std::endl;
		if(nfcID.length() > 0) {
			//std::cout << "Card ID = " << nfcID << std::endl;
			std::cout << "search in DB for " << nfcID << std::endl;
			//char tmp[] = "893j";
			//std::cout << strcmp(tmp,"8804689C") << std::endl;
			const char *c = nfcID.c_str();
			int cmp = strcmp(c,"8804279A");
			std::cout << "cmp = " << cmp << std::endl;
			if(nfcID=="8804279A") {
				std::cout << "TAG 1" << std::endl;
			}
		}
		delay(100);
	}
	return 0;
}

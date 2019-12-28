#include <wiringPi.h>
#include <wiringPiSPI.h>
#include <stdio.h>
#include <stdint.h>
#include <stdbool.h>
#include <string.h>

#include <iostream>

#include "RC522.c"

void IC_test ( void )
{
	char cStr [ 30 ];
	uint8_t ucArray_ID [ 4 ];    /*先后存放IC卡的类型和UID(IC卡序列号) // IC card type and UID (IC card serial number)*/
	uint8_t ucStatusReturn;      /*返回状态 // Return status*/
	static uint8_t ucLineCount = 0;
	while ( 1 ) {
		/*Find card*/
		if ( ( ucStatusReturn = PcdRequest ( PICC_REQALL, ucArray_ID ) ) != MI_OK ) { /*若失败再次寻卡 //If you fail to find the card again*/
			ucStatusReturn = PcdRequest ( PICC_REQALL, ucArray_ID );
		}

		if ( ucStatusReturn == MI_OK  ) {
			/*防冲撞（当有多张卡进入读写器操作范围时，防冲突机制会从其中选择一张进行操作） // Anti-collision (when multiple cards enter the reader's operating range, the anti-collision mechanism will select one of them to operate)*/
			if ( PcdAnticoll ( ucArray_ID ) == MI_OK ) {
				sprintf ( cStr, "The Card ID is: %02X%02X%02X%02X",
				          ucArray_ID [ 0 ],
				          ucArray_ID [ 1 ],
				          ucArray_ID [ 2 ],
				          ucArray_ID [ 3 ] );
				printf ( "%s\r\n", cStr );

				ucLineCount ++;
				if ( ucLineCount == 17 ) {
					ucLineCount = 0;
				}
			}
		}
	}
}

int main(void)
{
	RC522_setup(7);
	PcdReset ();
	M500PcdConfigISOType('A');
	printf ( "Start NFC scan\r\n" );
	std::cout << "some C++" << std::endl;
	while(1) {
		IC_test();
		delay(100);
	}
}

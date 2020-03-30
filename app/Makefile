CC = g++
CPPFLAGS = -Wall -g 
PROG = main.run
OBJS = main.o RC522.o database.o tts.o wav.o nfc.o tag_management.o

all: $(PROG)

$(PROG):
	$(CC) $(CPPFLAGS) -c -o RC522.o RC522.c
	$(CC) $(CPPFLAGS) -c -o database.o database.cpp
	$(CC) $(CPPFLAGS) -c -o tts.o tts.cpp
	$(CC) $(CPPFLAGS) -c -o wav.o wav.cpp
	$(CC) $(CPPFLAGS) -c -o nfc.o nfc.cpp
	$(CC) $(CPPFLAGS) -c -o tag_management.o tag_management.cpp
	
	g++ -fPIC -li2c -c MPU6050.cpp -o MPU6050.o
	g++ -shared -o libMPU6050.so MPU6050.o
	install -m 755 -p libMPU6050.so /usr/lib/
	install -m 644 -p MPU6050.h /usr/include/
	
	$(CC) $(CPPFLAGS) -c -o main.o main.cpp
	$(CC) $(CPPFLAGS) $(OBJS) -o $(PROG) -lwiringPi -lsqlite3 -lflite -lsfml-audio -lsfml-window -lsfml-system  -lMPU6050 -pthread -li2c
	rm -f *.o
	rm -f *.so
	
clean:
	rm -f *~ *.o $(PROG)

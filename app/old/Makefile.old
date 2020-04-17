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
	$(CC) $(CPPFLAGS) -c -o main.o main.cpp
	$(CC) $(CPPFLAGS) $(OBJS) -o $(PROG) -lwiringPi -lsqlite3 -lflite -lsfml-audio -lsfml-window -lsfml-system
	
clean:
	rm -f *~ *.o $(PROG)

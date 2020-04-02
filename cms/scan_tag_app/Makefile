CC = g++
CPPFLAGS = -Wall -g 
PROG = main.run
OBJS = main.o RC522.o nfc.o

all: $(PROG)

$(PROG):
	$(CC) $(CPPFLAGS) -c -o RC522.o RC522.c
	$(CC) $(CPPFLAGS) -c -o nfc.o nfc.cpp
	

	$(CC) $(CPPFLAGS) -c -o main.o main.cpp
	$(CC) $(CPPFLAGS) $(OBJS) -o $(PROG) -lwiringPi -pthread -ljsoncpp
	rm -f *.o
	rm -f *.so
	
clean:
	rm -f *~ *.o $(PROG)

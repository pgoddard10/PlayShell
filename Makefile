CC = g++
CPPFLAGS = -Wall -g 
PROG = main.run
OBJS = RC522.o main.o

all: $(PROG)

$(PROG):
	$(CC) $(CPPFLAGS) -c -o RC522.o RC522.c
	$(CC) $(CPPFLAGS) -c -o main.o main.cpp
	$(CC) $(CPPFLAGS) $(OBJS) -o $(PROG) -lwiringPi -lsqlite3 -lflite -lsfml-audio -lsfml-window -lsfml-system
	
clean:
	rm -f *~ *.o $(PROG)

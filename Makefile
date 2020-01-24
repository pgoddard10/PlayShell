GCC=g++
CPPFLAGS=-Wall -g 
PROG=main.run

all:
	$(GCC) $(CPPFLAGS) -o $(PROG) main.cpp -lwiringPi -lsqlite3 -lflite
	
clean:
	rm -f *~ *.o $(PROG) core a.out

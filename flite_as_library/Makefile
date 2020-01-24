CC = gcc
CFLAGS = -Wall -g
#CFLAGS for verbose/debugging -v -Wl,-v
OBJS = func.o main.o
PROG = run
LOCALLIBDIR = flite_inc
LDFLAGS = -L$(LOCALLIBDIR)/lib
#-L$(FLITEDIR)/build/armv6l-linux-gnueabihf/lib
LDLIBS  = -lflite_cmu_us_kal -lflite_usenglish -lflite_cmulex -lflite -lm -lasound

all: $(PROG)

$(PROG): 
	$(CC) $(CFLAGS) -c -o func.o func.c $(LDFLAGS) $(LDLIBS)
	$(CC) $(CFLAGS) -c -o main.o main.c $(LDFLAGS) $(LDLIBS)
	$(CC) $(CFLAGS) $(OBJS) -o $@ $(LDFLAGS) $(LDLIBS)

func.o: func.h
main.o: func.h

clean:
	rm -f *~ *.o $(PROG) core a.out
	#rm -f *.o $(PROG)

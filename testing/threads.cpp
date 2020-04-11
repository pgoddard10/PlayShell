#include <stdio.h>
#include <stdint.h>
#include <stdbool.h>
#include <string.h>
#include <iostream>
#include <fstream>
#include <jsoncpp/json/json.h>

// C program to demonstrates cancellation of another thread  
// using thread id 
#include <stdio.h> 
#include <unistd.h> 
#include <sys/types.h> 
#include <pthread.h> 
  
// To Count 
int counter = 0;  
  
// for temporary thread which will be  
// store thread id of second thread 
pthread_t tmp_thread;  
  
// thread_one call func 
void* func(void* p)  
{ 
    while (1) { 
  
        printf("\t\tthread number one\n"); 
        sleep(1);   // sleep 1 second 
        counter++;    
        
        // for exiting if counter = = 5 
        if (counter == 5) { 
  
            // for cancel thread_two 
            pthread_cancel(tmp_thread);  
  
            // for exit from thread_one  
            pthread_exit(NULL);   
        } 
    } 
} 
  
// thread_two call func2 
void* func2(void* p)  
{ 
  
    // store thread_two id to tmp_thread 
    tmp_thread = pthread_self();  
  
    while (1) { 
        printf("\t\tthread Number two\n"); 
        sleep(1); // sleep 1 second 
    } 
} 
  
// Driver code 
int main() 
{ 
  
    // declare two thread 
    pthread_t thread_one, thread_two;  
  
    // create thread_one 
    printf("create thread_one\n"); 
    pthread_create(&thread_one, NULL, func, NULL); 
  
    // create thread_two  
    printf("create thread_two \n"); 
    pthread_create(&thread_two, NULL, func2, NULL); 

	while(1) {
    	printf("main\n");
        sleep(1); // sleep 1 second ** MUST SLEEP IN ORDER TO PAUSE THE THREAD AND LET THE OTHER THREADS RUN
	}
  
    // // waiting for when thread_one is completed 
    // printf("waiting for when thread_one is completed \n"); 
    // pthread_join(thread_one, NULL);
  
    // // waiting for when thread_two is completed 
    // printf("waiting for when thread_two is completed \n"); 
    // pthread_join(thread_two, NULL);
  
}

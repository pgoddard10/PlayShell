#include "func.h"

cst_voice * register_cmu_us_kal(const char *voxdir);
int generate_tts(int argc, char **argv)
{

    cst_voice *v;
    flite_init();
    v = register_cmu_us_kal(NULL);
    flite_file_to_speech(argv[1],v,"play");
    return 0;
}

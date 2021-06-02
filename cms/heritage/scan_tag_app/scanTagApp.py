import json
import time

while(1):
    print("Please scan an NFC tag...")

    # code to get NFC tag ID goes here...
    nfcTagId = "87G4KNJ09F"

    #once NFC tag is scanned:

    with open('./json_exchange/content.json') as f:
        data = json.load(f)

    print("content.json opened and content ID obtained: ", data['id'])
    
    import json
    data = {
        'id': data['id'],
        'nfcTag': nfcTagId,
        'name': data['name'],
        'item': data['item'],
    }

    with open('./json_exchange/nfcData.json', 'w') as outfile:
        json.dump(data, outfile)
        print("All data saved and ready to be processed...")

    time.sleep(5)
import requests
import json
import time

def get_data_and_store_in_json():
    base_url = "https://granturi.imm.gov.ro/api/sun/transparency/transparency"
    page_number = 1
    size = 100
    call_id = "SUN_ED_2022_PILON_1"
    data = []

    try:
        with open('aplicanti.json') as json_file:
            data = json.load(json_file)
    except FileNotFoundError:
        pass

    cui_data_map = {item['cui']: item for item in data}

    while True:
        params = {
            "page": page_number,
            "size": size,
            "sort": ",asc",
            "callId": call_id
        }

        response = requests.get(base_url, params=params)

        if response.status_code != 200:
            print("Unable to fetch data from server. HTTP Status code:", response.status_code)
            break

        response_json = response.json()

        # Extract the data and handle duplicates
        for item in response_json['content']:
            cui_data_map[item['cui']] = item

        # Write the data to a JSON file
        with open('aplicanti.json', 'w') as json_file:
            json.dump(list(cui_data_map.values()), json_file)

        # Print progress
        print(f'Page {page_number} processed and data written to file.')

        # If this is the last page, break the loop
        if response_json['last'] == True:
            break

        # Otherwise, move on to the next page
        page_number += 1

        # Delay for 3 seconds
        time.sleep(3)

get_data_and_store_in_json()


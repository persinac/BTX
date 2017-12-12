from configparser import ConfigParser
import json
import os

def config(filename='database.json'):
    # get section, default to postgresql
    filename = os.getcwd() + "/config/" + filename
    # db = {}
    with open(filename) as json_data_file:
        data = json.load(json_data_file)
    return data
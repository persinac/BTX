from configparser import ConfigParser
import json
import os

def config(filename='database.json'):
    # get section, default to postgresql
    filename = "/var/www/html/srcpy/config/" + filename
    # db = {}
    with open(filename) as json_data_file:
        data = json.load(json_data_file)
    return data
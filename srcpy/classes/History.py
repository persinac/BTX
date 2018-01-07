import sys
import logging

class History:

    def __init__(self, id, coin, market, description, history_ref_key, timestamp):
        self.id = id
        self.coin = coin
        self.market = market
        self.description = description
        self.history_ref_key = history_ref_key
        self.timestamp = timestamp

    def __enter__(self):
        return self

    def createcommadelimitedvalueforinsert(self):
        retVal = ""
        retVal += "'" + self.coin + "'"
        retVal += ", '" + self.market + "'"
        retVal += ",'" + self.description + "'"
        retVal += "," + str(self.history_ref_key) + ""
        retVal += "," + str(self.timestamp) + ""
        return retVal
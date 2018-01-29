class BTXCoinHeader:
    def __init__(self, id, coin, market, coinname, mintradesize,
                         txfee, minconfirmation, isactive, btxTimestamp, timestamp, logourl):
        self.id = id
        self.coin = coin
        self.market = market
        self.coinname = coinname
        self.mintradesize = mintradesize
        self.txfee = txfee
        self.minconfirmation = minconfirmation
        self.isactive = isactive
        self.btxTimestamp = btxTimestamp
        self.timestamp = timestamp
        self.logourl = logourl

    def __enter__(self):
        return self

    def createcommadelimitedvalueforinsert(self):
        retVal = ""
        retVal += "'" + self.coin + "'"
        retVal += ", '" + self.market + "'"
        retVal += ",'" + self.coinname + "'"
        retVal += "," + str(self.mintradesize) + ""
        retVal += "," + str(self.txfee) + ""
        retVal += "," + str(self.minconfirmation) + ""
        retVal += "," + str(self.isactive) + ""
        retVal += "," + str(self.btxTimestamp) + ""
        retVal += "," + str(self.timestamp) + ""
        retVal += ",'" + str(self.logourl) + "'"
        return retVal
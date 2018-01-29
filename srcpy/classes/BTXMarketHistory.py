class BTXMarketHistory:
    def __init__(self, id, coin, market, volume, value,
                         usdvalue, high, low, lastsell, currentbid,
                         openbuyorders, opensellorders, btxtimestamp, timestamp):
        self.id = id
        self.coin = coin
        self.market = market
        self.volume = volume
        self.value = value
        self.usdvalue = usdvalue
        self.high = high
        self.low = low
        self.lastsell = lastsell
        self.currentbid = currentbid
        self.openbuyorders = openbuyorders
        self.opensellorders = opensellorders
        self.btxtimestamp = btxtimestamp
        self.timestamp = timestamp

    def __enter__(self):
        return self

    def createcommadelimitedvalueforinsert(self):
        retVal = "'%s', '%s', %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s" % (
            self.coin, self.market, self.volume, self.value, self.usdvalue, self.high
            , self.low, self.lastsell, self.currentbid, self.openbuyorders, self.opensellorders
            , self.btxtimestamp, self.timestamp
        )
        return retVal
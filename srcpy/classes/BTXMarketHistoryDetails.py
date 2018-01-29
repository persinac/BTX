class BTXMarketHistoryDetails:
    def __init__(self, id, btxid, coin, market, quantity, value,
                         usdvalue, total, filltype, ordertype, btxtimestamp):
        self.id = id
        self.btxid = btxid
        self.coin = coin
        self.market = market
        self.quantity = quantity
        self.value = value
        self.usdvalue = usdvalue
        self.total = total
        self.filltype = filltype
        self.ordertype = ordertype
        self.btxtimestamp = btxtimestamp

    def __enter__(self):
        return self

    def createcommadelimitedvalueforinsert(self):
        retVal = "%s, '%s', '%s', %s, %s, %s, %s, %s, %s, %s" % (
            self.btxid, self.coin, self.market, self.quantity, self.value, self.usdvalue, self.total
            , self.filltype, self.ordertype, self.btxtimestamp
        )
        return retVal
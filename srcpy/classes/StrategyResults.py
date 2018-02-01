class StrategyResults:
    def __init__(self,
                 id, buycomboid, sellcomboid, tickervalue, strategyfile, configfile, twohrprofit, twohrprofitperc,
                 fourhrprofit, fourhrprofitperc, eighthrprofit, eighthrprofitperc, totalprofit, totalprofitperc,
                 startedon, endedon):
        self.id = id
        self.buycomboid = buycomboid
        self.sellcomboid = sellcomboid
        self.tickervalue = tickervalue
        self.strategyfile = strategyfile
        self.configfile = configfile
        self.twohrprofit = twohrprofit
        self.twohrprofitperc = twohrprofitperc
        self.fourhrprofit = fourhrprofit
        self.fourhrprofitperc = fourhrprofitperc
        self.eighthrprofit = eighthrprofit
        self.eighthrprofitperc = eighthrprofitperc
        self.totalprofit = totalprofit
        self.totalprofitperc = totalprofitperc
        self.startedon = startedon
        self.endedon = endedon

    def __enter__(self):
        return self

    def createcommadelimitedvalueforinsert(self):
        retVal = "%s,%s,%s,%s," \
                 "%s,%s,%s,%s," \
                 "%s,%s,%s,%s," \
                 "%s,'%s','%s'"%(
            self.buycomboid, self.sellcomboid, self.tickervalue, self.strategyfile,
            self.configfile, self.twohrprofit, self.twohrprofitperc, self.fourhrprofit,
            self.fourhrprofitperc, self.eighthrprofit, self.eighthrprofitperc, self.totalprofit,
            self.totalprofitperc, self.startedon, self.endedon
        )
        return retVal
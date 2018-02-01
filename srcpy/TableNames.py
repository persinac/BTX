BTX_TBL_TESTING_LEDGER = 'btxledger'
BTX_TBL_COINS_TO_WATCH = 'btxcoinswatch'
BTX_TBL_MARKET_HISTORY = [
        'btxmarkethistory',
        "(coin,market,volume,\"value\",\"usdValue\",high,low,\"lastSell\",\"currentBid\",\"openBuyOrders\",\"openSellOrders\",btxtimestamp,\"timestamp\")",
        "(id,coin,market,volume,\"value\",\"usdValue\",high,low,\"lastSell\",\"currentBid\",\"openBuyOrders\",\"openSellOrders\",btxtimestamp,\"timestamp\")"
    ]

BTX_TBL_COIN_MARKET_HISTORY_DETAILS = [
        'btxcoinmarkethistorydetails',
        "(btxid,coin,market,quantity,\"value\",\"usdValue\",total,filltype,ordertype,btxtimestamp)",
        "(id,btxid,coin,market,quantity,\"value\",\"usdValue\",total,filltype,ordertype,btxtimestamp)"
    ]

BTX_TBL_RUNNING_TOTALS = 'btxrunningtotals'
BTX_TBL_TRANSACTIONS= 'btxtransactions'
BTX_TBL_COIN_HEADER = [
        'btxcoinheader',
        "(coin,market,coinname,mintradesize,txfee,minconfirmation,isactive,btxtimestamp,timestamp,logourl)",
        "(id,coin,market,coinname,mintradesize,txfee,minconfirmation,isactive,btxtimestamp,timestamp,logourl)"
    ]

BTX_TBL_HISTORY = ['btxhistory',
        "(coin,market,description,history_ref_key,timestamp)",
        "(id,coin,market,description,history_ref_key,timestamp)"
    ]

BTX_TBL_HISTORY_REF = [
        'btxhistoryref',
        "(type,subtype,name,isactive)",
        "(id,type,subtype,name,isactive)"
    ]

BTX_TBL_CALENDAR_EVENTS = [
        'calevents',
        "(idfromsource,source,coin,eventaddedon,eventdate,title,description,validity,typeofevent,byoron,proofofsource)",
        "(id,idfromsource,source,coin,eventaddedon,eventdate,title,description,validity,typeofevent,byoron,proofofsource)"
    ]

BTX_TBL_CALENDAR_EVENT_TYPES = [
        'caleventtypes',
        "(label,weight,isactive)",
        "(id,label,weight,isactive)"
    ]

BTX_TBL_IND_REFERENCE = [
        'indreference',
        "(code,longname,timeperiodvalue,bullish,bearish,isactive,addedon)",
        "(id,code,longname,timeperiodvalue,bullish,bearish,isactive,addedon)"
    ]

BTX_TBL_IND_MEASUREMENT_COMBINATIONS = [
        'indmeasurementcombinations',
        "(indicatorid,comparisonoperator,comparisonvalue,compareagainstotheroperator,otherindicatorid,buy,sell,createdon,createdby)",
        "(id,indicatorid,comparisonoperator,comparisonvalue,compareagainstotheroperator,otherindicatorid,buy,sell,createdon,createdby)"
    ]

BTX_TBL_IND_COMBINATION_MAPPING = [
        'indcombinationmapping',
        "(combinationid,indrefid,buystrat,sellstrat,createdon)",
        "(id,combinationid,indrefid,buystrat,sellstrat,createdon)"
    ]

BTX_TBL_IND_STRATEGY_RESULTS = [
        'indstrategyresults',
        "(buycomboid, sellcomboid, tickervalue, strategyfile, configfile, twohrprofit, twohrprofitperc, fourhrprofit, fourhrprofitperc, eighthrprofit, eighthrprofitperc, totalprofit, totalprofitperc, startedon, endedon)",
        "(id, buycomboid, sellcomboid, tickervalue, strategyfile, configfile, twohrprofit, twohrprofitperc, fourhrprofit, fourhrprofitperc, eighthrprofit, eighthrprofitperc, totalprofit, totalprofitperc, startedon, endedon)"
    ]
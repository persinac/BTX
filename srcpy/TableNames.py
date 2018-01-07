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

#!/usr/bin/env python

import sys
import logging
sys.path.append('/var/www/html/srcpy/bittrex')

logging.basicConfig(filename='/var/www/html/example.log',level=logging.DEBUG)
logger = logging.getLogger()
handler = logging.StreamHandler()
formatter = logging.Formatter(
        '%(asctime)s %(name)-12s %(levelname)-8s %(message)s')
handler.setFormatter(formatter)
logger.addHandler(handler)
logger.setLevel(logging.DEBUG)



class reg(object):
	def __init__(self, cursor, registro):
		for (attr, val) in zip((d[0] for d in cursor.description), registro) :
			setattr(self, attr, val)

config = {
	'user': 'bittrex_user',
	'password': 'password123',
	'host': 'localhost',
	'database': 'bittrex'
}

market = 'BTC'
coinsToWatch = ['EMC2', 'BAT', 'XRP', 'MONA', 'ETH','NEO','OMG','LTC']

try:
    from bittrex import Bittrex
    import psycopg2
    from datetime import datetime
    import jsonpickle
    from pprint import pprint
    from datetime import timedelta

    connect_str = "dbname='bittrex' user='bittrex_user' host='localhost' password='password123'"
    conn = psycopg2.connect(connect_str)
    cursor = conn.cursor()
    cursor.execute("select * from btxmarkethistory")
    rows = cursor.fetchall()
    colnames = [desc[0] for desc in cursor.description]
    # print(colnames)

    #set API key
    bittrex = Bittrex('e281fee0429e428b9a76f63cc842f12d', 'a131b56ec7f24696a74c6b6da853d1eb')

    #get balance - just to test integration
    actual = bittrex.get_balance('BTC')

    currentTime = datetime.now()
    previousMinuteTime_nonFormat = currentTime - timedelta(minutes=1)
    previousMinuteReplaceSeconds = previousMinuteTime_nonFormat.replace(second=0, microsecond=0)

    currMinuteTime_nonFormat = currentTime
    currMinuteTimeReplaceSeconds = currMinuteTime_nonFormat.replace(second=0, microsecond=0)

    dataTuple = []
    for value in coinsToWatch:
        ## get various market summaries
        specificMarketHistory = bittrex.get_market_history((market + '-' + str(value)), 30)
        # print(str(marketSummary['result']))
        if specificMarketHistory['success'] == True:
            for valuetwo in specificMarketHistory['result']:
                strippedMSIdx = str(valuetwo['TimeStamp']).find(".")
                parsedTime = ""
                if strippedMSIdx > 0:
                    substrTime = str(valuetwo['TimeStamp'])[:strippedMSIdx]
                    parsedTime = datetime.strptime(substrTime, "%Y-%m-%dT%H:%M:%S")
                else:
                    parsedTime = datetime.strptime(substrTime, "%Y-%m-%dT%H:%M:%S")
                compareLow = parsedTime >= previousMinuteReplaceSeconds
                compareHigh = parsedTime < currMinuteTimeReplaceSeconds
                # print(str(parsedTime) + " >= " + str(previousMinuteReplaceSeconds) +": "+ str(compareLow))
                # print(str(parsedTime) + " < " + str(currMinuteTimeReplaceSeconds) + ": " + str(compareHigh))

                # another way to compare this so that we don't get duplicates is
                # to query the DB for all btxids and only insert
                # the values that don't already exist in our DB
                # for now this should be fine...check for duplicates later
                if compareLow:
                    if compareHigh:
                        ###
                        # btxid, coin, market,
                        # quantity, value, total,
                        # filltype, ordertype, btxtimestamp
                        ###
                        mytuple = (valuetwo['Id'], value, market,
                        valuetwo['Quantity'], valuetwo['Price'],
                         valuetwo['Total'], valuetwo['FillType'],
                         valuetwo['OrderType'], valuetwo['TimeStamp'])
                        dataTuple.append(mytuple)


    rowsToInsert = ",".join(
        cursor.mogrify('(%s,%s,%s,%s,%s,%s,%s,%s,%s)', thing).decode('UTF-8')
        for thing in dataTuple
    )
    # print(str(rowsToInsert))
    cursor.execute("""INSERT INTO btxcoinmarkethistorydetails(
    btxid, coin, market, quantity, value, total, filltype, ordertype, btxtimestamp) VALUES """ + rowsToInsert)
    conn.commit()

except:
    logger.exception('Main Exception handler')
    raise

import sys
import os
import logging
import json
import globals
import TableNames
from connection import PGConnect
from calendar import timegm
from datetime import datetime
from classes import History
from classes import BTXMarketHistory
from CRUD.create import BTXCreator
from bittrex import Bittrex
from common import Utilities

logging.basicConfig(filename='/var/www/html/example.log',level=logging.DEBUG)
logger = logging.getLogger()
handler = logging.StreamHandler()
formatter = logging.Formatter(
        '%(asctime)s %(name)-12s %(levelname)-8s %(message)s')
handler.setFormatter(formatter)
logger.addHandler(handler)
logger.setLevel(logging.DEBUG)

pgconn = PGConnect.DBase()
currFile = os.path.splitext(__file__)[0]
fileNameFormat = "%Y_%m_%d_%H:%M:%S"
sqlSelect = "SELECT * from %s" % (TableNames.BTX_TBL_HISTORY_REF[0])
sqlWhere = "WHERE name = '%s' and type = '%s'" % (currFile, globals.HISTORY_REF_TYPE_CRON_JOB)
sqlFull = sqlSelect + " " + sqlWhere
historyRefVal = pgconn.query(sqlFull)
apiFileDataVerifyName = globals.API_DATA_STORAGE_BASE + globals.API_DATA_GET_MARKET_SUMMARIES_DIRECTORY + str(datetime.now().strftime(fileNameFormat))

historyTuple = []
btxMarketSummariesTuple = []

#set API key
bittrex = Bittrex('1','1')
#get balance - just to test integration
# actual = bittrex.get_balance('BTC')
# get_marketsummary
btcUSDTCall = bittrex.get_marketsummary("USDT-BTC")
btcUSDValue = 0.00
if btcUSDTCall['success'] == True:
    btcUSDValue = btcUSDTCall['result'][0]['Last']

getMarketSummaries = bittrex.get_market_summaries()
if getMarketSummaries['success'] == True:
        apiFileDataVerifyName += "___SUCCESS"
        results = getMarketSummaries['result']
        searchParty = ['BTC-', 'USDT-']
        for searchFor in searchParty:
            for result in results:
                pos = str(result['MarketName']).find(searchFor)
                if pos >= 0:
                    historyDescription = ""
                    usdtConversion = 0.00
                    if searchFor == "USDT-":
                        usdtConversion = result['Last']
                    else:
                        usdtConversion = Utilities.Utilities.calculateusdvalue(btcUSDValue, result['Last'])

                    coin = str(result['MarketName'])[pos+len(searchFor):]
                    market = str(result['MarketName'])[:pos+len(searchFor)-1]

                    strippedMSIdx = str(result['Created']).find(".")
                    parsedTime = ""
                    if strippedMSIdx > 0:
                            substrTime = str(result['Created'])[:strippedMSIdx]
                            parsedTime = datetime.strptime(substrTime, "%Y-%m-%dT%H:%M:%S")
                    else:
                            parsedTime = datetime.strptime(result['Created'], "%Y-%m-%dT%H:%M:%S")

                    btxMarketSummary = BTXMarketHistory.BTXMarketHistory(
                            -1, coin, market, result['BaseVolume'],
                        result['Last'], usdtConversion, result['High'], result['Low'],
                        result['Last'], result['Bid'], result['OpenBuyOrders'],
                        result['OpenSellOrders'], parsedTime.timestamp(),
                        timegm(datetime.utcnow().utctimetuple())
                    )
                    btxMarketSummariesTuple.append(btxMarketSummary)
                    historyDescription = "Insert Market Summary."
                    # Initialize BTXHistory obj to capture statement
                    historyObj = History.History(
                            -1
                            , btxMarketSummary.coin
                            , btxMarketSummary.market
                            , historyDescription
                            , historyRefVal[0][0]
                            , timegm(datetime.utcnow().utctimetuple())
                    )
                    historyTuple.append(historyObj)

        insertStmtn = BTXCreator.BTXCreator.buildinsertstatement(btxMarketSummariesTuple, TableNames.BTX_TBL_MARKET_HISTORY[1], TableNames.BTX_TBL_MARKET_HISTORY[0])
        numOfInserts = len(btxMarketSummariesTuple)
        if numOfInserts > 0:
            pgconn.insert(insertStmtn)

        retValToEcho = str(datetime.now()) + " | Inserted " + str(numOfInserts) + " rows into BTXCoinMarketHistory."
else:
        apiFileDataVerifyName += "___FAIL"
        retValToEcho = str(datetime.now()) + " | CURL Call to bittrex API: /public/get_market_summaries failed"

# Initialize final BTXHistory obj to capture end of file statement
historyObj = History.History(
    -1
        , "ALL"
    , "ALL"
    , retValToEcho
    , historyRefVal[0][0]
    , timegm(datetime.utcnow().utctimetuple())
)
historyTuple.append(historyObj)
insertStmtn = BTXCreator.BTXCreator.buildinsertstatement(historyTuple, TableNames.BTX_TBL_HISTORY[1], TableNames.BTX_TBL_HISTORY[0])
pgconn.insert(insertStmtn)
print(retValToEcho)
apiFileDataVerifyName += ".json"
file = open(apiFileDataVerifyName, "w")
file.write(json.dumps(btxMarketSummariesTuple, default=lambda o: o.__dict__))
file.close()
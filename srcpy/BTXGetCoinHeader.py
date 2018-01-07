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
from classes import BTXCoinHeader
from CRUD.create import BTXCreator
from bittrex import Bittrex

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
apiFileDataVerifyName = globals.API_DATA_STORAGE_BASE + globals.API_DATA_GET_COIN_HEADER_DIRECTORY + str(datetime.now().strftime(fileNameFormat));
historyTuple = []
btxCoinHeaderTuple = []

#set API key
bittrex = Bittrex('1','1')
#get balance - just to test integration
# actual = bittrex.get_balance('BTC')
getMarkets = bittrex.get_markets()
if getMarkets['success'] == True:
        apiFileDataVerifyName += "___SUCCESS"
        results = getMarkets['result']
        listOfHeaders = []
        listOfHeadersToUpdate = []
        for result in results:
                strippedMSIdx = str(result['Created']).find(".")
                parsedTime = ""
                if strippedMSIdx > 0:
                        substrTime = str(result['Created'])[:strippedMSIdx]
                        parsedTime = datetime.strptime(substrTime, "%Y-%m-%dT%H:%M:%S")
                else:
                        parsedTime = datetime.strptime(result['Created'], "%Y-%m-%dT%H:%M:%S")
                isActive = 0
                if result['IsActive'] == True:
                        isActive = 1

                btxCoinHeader = BTXCoinHeader.BTXCoinHeader(
                        -1, result['MarketCurrency'], result['BaseCurrency'], result['MarketCurrencyLong'], result['MinTradeSize'],
                        0, 0, isActive, parsedTime.timestamp(), timegm(datetime.utcnow().utctimetuple()), result['LogoUrl']
                )
                btxCoinHeaderTuple.append(btxCoinHeader)
                # Initialize final BTXHistory obj to capture end of file statement
                historyObj = History.History(
                        -1
                        , btxCoinHeader.coin
                        , btxCoinHeader.market
                        , "Insert up to date coin header reference."
                        , historyRefVal[0][0]
                        , timegm(datetime.utcnow().utctimetuple())
                )
                historyTuple.append(historyObj)

        insertStmtn = BTXCreator.BTXCreator.buildinsertstatement(btxCoinHeaderTuple, TableNames.BTX_TBL_COIN_HEADER[1], TableNames.BTX_TBL_COIN_HEADER[0])
        numOfInserts = len(btxCoinHeaderTuple)
        if numOfInserts > 0:
                pgconn.insert(insertStmtn)

        retValToEcho = str(datetime.now()) + " | Inserted " + str(numOfInserts) + " rows into BTXCoinHeader."
else:
        apiFileDataVerifyName += "___FAIL"
        retValToEcho = str(datetime.now()) + " | CURL Call to bittrex API: /public/getmarkets failed"

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
insertStmtn = BTXCreator.BTXCreator.buildinsertstatement(historyTuple, TableNames.BTX_TBL_HISTORY_TBL[1], TableNames.BTX_TBL_HISTORY_TBL[0])
pgconn.insert(insertStmtn)
print(retValToEcho)
apiFileDataVerifyName += ".json"
file = open(apiFileDataVerifyName, "w")
file.write(json.dumps(btxCoinHeaderTuple, default=lambda o: o.__dict__))
file.close()
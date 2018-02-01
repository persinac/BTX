import sys
import os
import logging
import json
import string
import globals
import TableNames
import time
import threading
from multiprocessing import Queue
from connection import PGConnect
from calendar import timegm
from datetime import datetime
from datetime import timedelta
from classes import History
from classes import BTXMarketHistoryDetails
from CRUD.create import BTXCreator
from bittrex import Bittrex
from common import Utilities

def fetch_parallel(searchFor, btcUSDValue, list, searchNum, historyRefVal):
    try:
        result = Queue()
        startTime = datetime.now()
        threads = [threading.Thread(target=getmarketsummarydetailsthread, args = (searchFor, btcUSDValue, market, searchNum, historyRefVal, result)) for market in list]
        for t in threads:
            t.start()
        for t in threads:
            t.join()

        endTime = datetime.now()
        print("________________THREAD________________")
        print("Duration: %s - %s" % (startTime, endTime))
        count = 1
        max = len(list)
        myArray = []
        while count < max:
            myArray.append(result.get())
            count += 1
        return myArray
    except (BrokenPipeError, IOError):
        pass

def getmarketsummarydetailsthread(searchFor, btcUSDValue, marketToSearch, searchNum, historyRefVal, queue):
    # explodeList = str.split(list, ",")
    marketResult = None
    explodeList = [marketToSearch]
    currentTime = datetime.now()
    previousMinuteTime_nonFormat = currentTime - timedelta(minutes=1)
    previousMinuteReplaceSeconds = previousMinuteTime_nonFormat.replace(second=0, microsecond=0)

    currMinuteTime_nonFormat = currentTime
    currMinuteTimeReplaceSeconds = currMinuteTime_nonFormat.replace(second=0, microsecond=0)

    listOfObjs = []
    listOfHistoryObjs = []
    retValToEcho = ""
    dataArr = []
    for fullmarket in explodeList:

        pos = str(fullmarket).find(searchFor)
        historyDescription = ""
        coin = str(fullmarket[pos + len(searchFor):])
        market = str(fullmarket[:pos + len(searchFor) - 1])
        bittrex = Bittrex('1', '1')
        specMarket = bittrex.get_market_history(fullmarket)
        if specMarket['success'] == True and specMarket['result'] is not marketResult:
            count = 0
            for row in specMarket['result']:
                # {
                # "Id":21450896,
                # "TimeStamp":"2017-11-30T23:06:50.86",
                # "Quantity":31.91489361,
                # "Price":0.00004991,
                # "Total":0.00159287,
                # "FillType":"PARTIAL_FILL",
                # "OrderType":"BUY"
                # }

                dateCompare = Utilities.Utilities.parsebtxtimestamp(row['TimeStamp'])
                if dateCompare >= previousMinuteReplaceSeconds:
                    if dateCompare < currMinuteTimeReplaceSeconds:
                        count += 1
                        usdtConversion = 0.00
                        if searchFor == "USDT-":
                            usdtConversion = row['Price']
                        else:
                            usdtConversion = Utilities.Utilities.calculateusdvalue(btcUSDValue, row['Price'])

                        fillType = globals.FILL_TYPE_FILL
                        orderType = globals.ORDER_TYPE_BUY
                        if row['FillType'] == "PARTIAL_FILL":
                            fillType = globals.FILL_TYPE_PARTIAL
                        if row['OrderType'] == "SELL":
                            orderType = globals.ORDER_TYPE_SELL

                        ## For history output - vanilla data
                        dataArr.append(row)

                        btxMarketHistory = BTXMarketHistoryDetails.BTXMarketHistoryDetails(
                            -1,
                            row['Id'],
                            coin,
                            market,
                            row['Quantity'],
                            row['Price'],
                            usdtConversion,
                            row['Total'],
                            fillType,
                            orderType,
                            dateCompare.timestamp()
                        )
                        listOfObjs.append(btxMarketHistory)
            if count > 0:
                historyDescription = "Insert Market Summary Details_thread (" + str(searchNum) + ") - " + str(count) + " rows"
            else:
                historyDescription = "Insert Market Summary Details_thread (" + str(searchNum) + ") - " + str(count) + " rows"
                historyDescription += " | Nothing found between: " + str(previousMinuteReplaceSeconds) + " and " + str(currMinuteTimeReplaceSeconds) + " | " + str(json.dumps(specMarket))[800:]

            # Initialize BTXHistory obj
            historyObj = History.History(-1,
                                         coin
                                         , market
                                         , historyDescription
                                         , historyRefVal[0][0]
                                         , timegm(datetime.utcnow().utctimetuple())
                                         )
            listOfHistoryObjs.append(historyObj)
        else:
            historyDescription = "Insert Market Summary Details_thread - (" + str(searchNum) + ") -  FAIL"
            historyDescription += " | " + str(json.dumps(specMarket))[800:]
            # Initialize BTXHistory obj
            historyObj = History.History(-1,
                                         coin
                                         , market
                                         , historyDescription
                                         , historyRefVal[0][0]
                                         , timegm(datetime.utcnow().utctimetuple())
                                         )
            listOfHistoryObjs.append(historyObj)

    numOfInserts = len(listOfHistoryObjs)
    hist_retValToEcho = " File Complete - History wrote " + str(numOfInserts) + " to table (excluding this row)"

    # Initialize final BTXHistory obj to capture end of file statement
    historyObj_all = History.History(-1,
                                 "ALL"
                                 , "ALL"
                                 , hist_retValToEcho
                                 , historyRefVal[0][0]
                                 , timegm(datetime.utcnow().utctimetuple())
                                 )
    listOfHistoryObjs.append(historyObj_all)
    # Generate Insert statement
    historyInsertStmnt = BTXCreator.BTXCreator.buildvaluesforinsertstatement(listOfHistoryObjs)
    insertStmtn = ""
    if len(listOfObjs) > 0:
        insertStmtn = BTXCreator.BTXCreator.buildvaluesforinsertstatement(listOfObjs)

    actualRetVal = {'marketInsertStatement':insertStmtn, 'historyInsertStatement':historyInsertStmnt, 'vanillaData':dataArr}
    queue.put(actualRetVal)

############################
# BEGIN SCRIPT
############################
startTime = datetime.now()
searchInterval = int(sys.argv[1])

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

sqlSelect = "SELECT * from %s" % (TableNames.BTX_TBL_HISTORY_REF[0])
sqlWhere = "WHERE name = '%s' and type = '%s'" % (currFile + "_thread", globals.HISTORY_REF_TYPE_CRON_JOB)
sqlFull = sqlSelect + " " + sqlWhere
historyRefVal_thread = pgconn.query(sqlFull)

apiFileDataVerifyName = globals.API_DATA_STORAGE_BASE + globals.API_DATA_GET_MARKET_SUMMARY_DETAILS_DIRECTORY + str(datetime.now().strftime(fileNameFormat))
apiFileDataVerifyName += "_(%s)_"%(searchInterval)
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
listOfUSDTMarketCoins = []
listOfBTCMarketCoins = []

### New Variables to use
marketInsertValues = ""
historyInsertValues = ""
vanillaDataForFileOuput = []

if getMarketSummaries['success'] == True:
    apiFileDataVerifyName += "___SUCCESS"
    results = getMarketSummaries['result']
    searchParty = ['BTC-', 'USDT-']
    for searchFor in searchParty:
        for result in results:
            pos = str(result['MarketName']).find(searchFor)
            if pos >= 0:
                # build a list of coins based on searchFor
                if searchFor == "USDT-":
                    listOfUSDTMarketCoins.append(result['MarketName'])
                else:
                    listOfBTCMarketCoins.append(result['MarketName'])

    usdtRetVal = ""
    btcListRetVal = ""
    valuesInsert = ""
    histValuesInsert = ""
    threadStart = datetime.now()
    # execute USDT market data script and let the cpu work on the BTC list
    if searchInterval < 0:
        usdtRetVal = fetch_parallel("USDT-", btcUSDValue, listOfUSDTMarketCoins, "USDT", historyRefVal_thread)
        decipherMe = usdtRetVal
        for i in range(len(decipherMe)):
            if i > 0:
                marketInsertValues += ","
                historyInsertValues += ","
            marketInsertValues += decipherMe[i]['marketInsertStatement']
            historyInsertValues += decipherMe[i]['historyInsertStatement']
            vanillaDataForFileOuput.append(decipherMe[i]['vanillaData'])
        marketInsertStmnt = BTXCreator.BTXCreator.buildmaininsertstatement(marketInsertValues,
                                                                            TableNames.BTX_TBL_COIN_MARKET_HISTORY_DETAILS[1],
                                                                            TableNames.BTX_TBL_COIN_MARKET_HISTORY_DETAILS[0],
                                                                           1)
        historyInsertStmnt = BTXCreator.BTXCreator.buildmaininsertstatement(historyInsertValues,
                                                                            TableNames.BTX_TBL_HISTORY[1],
                                                                            TableNames.BTX_TBL_HISTORY[0],
                                                                            1)
        ## Clean up empty double commas - should never happen with history
        marketInsertStmnt = Utilities.Utilities.removestringoccurences(marketInsertStmnt, ",,", ",")
        historyInsertStmnt = Utilities.Utilities.removestringoccurences(historyInsertStmnt, ",,", ",")

        print(marketInsertStmnt[-1:] == ",")
        while marketInsertStmnt[-1:] == ",":
            marketInsertStmnt = marketInsertStmnt[len(marketInsertStmnt) - 1:]

        try:
            pgconn.insert(marketInsertStmnt)
            pgconn.insert(historyInsertStmnt)
        except Exception as inst:
            logger.exception('Main Exception handler')
            print(type(inst))  # the exception instance
            print(inst.args)  # arguments stored in .args
            print(inst)
            raise
        historyDescription = "Insert Market Summary Details: USDT Interval: " + str(searchInterval)
    else:
        splitMod = 3
        btcList = []
        for i in range(len(listOfBTCMarketCoins)):
            if i % splitMod == searchInterval:
                btcList.append(listOfBTCMarketCoins[i])

        btcListRetVal = fetch_parallel("BTC-", btcUSDValue, btcList, searchInterval, historyRefVal_thread)
        decipherMe = btcListRetVal
        for i in range(len(decipherMe)):
            if i > 0:
                marketInsertValues += ","
                historyInsertValues += ","
            marketInsertValues += decipherMe[i]['marketInsertStatement']
            historyInsertValues += decipherMe[i]['historyInsertStatement']
            vanillaDataForFileOuput.append(decipherMe[i]['vanillaData'])

        marketInsertStmnt = BTXCreator.BTXCreator.buildmaininsertstatement(marketInsertValues,
                                                                           TableNames.BTX_TBL_COIN_MARKET_HISTORY_DETAILS[1],
                                                                           TableNames.BTX_TBL_COIN_MARKET_HISTORY_DETAILS[0],
                                                                           1)
        historyInsertStmnt = BTXCreator.BTXCreator.buildmaininsertstatement(historyInsertValues,
                                                                            TableNames.BTX_TBL_HISTORY[1],
                                                                            TableNames.BTX_TBL_HISTORY[0],
                                                                            1)
        ## Clean up empty double commas - should never happen with history
        marketInsertStmnt = Utilities.Utilities.removestringoccurences(marketInsertStmnt, ",,", ",")
        historyInsertStmnt = Utilities.Utilities.removestringoccurences(historyInsertStmnt, ",,", ",")
        while marketInsertStmnt[-1:] == ",":
            marketInsertStmnt = marketInsertStmnt[:-1]
        try:
            if len(marketInsertValues) > 0:
                pgconn.insert(marketInsertStmnt)
                pgconn.insert(historyInsertStmnt)
        except Exception as inst:
            logger.exception('Main Exception handler')
            print(type(inst))  # the exception instance
            print(inst.args)  # arguments stored in .args
            print(inst)
            print(marketInsertStmnt)
            raise
        historyDescription = "Insert Market Summary Details: BTC Interval: " + str(searchInterval)

    threadEnd = datetime.now()
    retValToEcho = "Retrieved data from Bittrex. Duration: %s - %s." % (str(threadStart), str(threadEnd))
else:
    apiFileDataVerifyName += "___FAIL"
    retValToEcho = str(datetime.now()) + " | CURL Call to bittrex API: /public/get_market_summaries failed"


pgconn.explicitclose()

endTime = datetime.now()
entireScriptRetVal = "Script complete. Duration: %s - %s | %s"%(startTime, endTime, retValToEcho)
# Initialize final BTXHistory obj to capture end of file statement
historyObj = History.History(
    -1
        , "ALL"
    , "ALL"
    , entireScriptRetVal
    , historyRefVal[0][0]
    , timegm(datetime.utcnow().utctimetuple())
)

historyTuple.append(historyObj)
insertStmtn = BTXCreator.BTXCreator.buildinsertstatement(historyTuple, TableNames.BTX_TBL_HISTORY[1], TableNames.BTX_TBL_HISTORY[0])
print(insertStmtn)
last_pgconn = PGConnect.DBase()
try:
    last_pgconn.insert(insertStmtn)
except Exception as inst:
    logger.exception('Main Exception handler')
    print(type(inst))  # the exception instance
    print(inst.args)  # arguments stored in .args
    print(inst)
    raise

last_pgconn.explicitclose()
apiFileDataVerifyName += ".json"
file = open(apiFileDataVerifyName, "w")
for item in vanillaDataForFileOuput:
    file.write(json.dumps(item, default=lambda o: o.__dict__))
file.close()



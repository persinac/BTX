import time
import numpy
import talib
import psycopg2
import sys
import logging
import math
import json
from datetime import datetime
from pprint import pprint
from datetime import timedelta
from config import config

logging.basicConfig(filename='/var/www/html/example.log',level=logging.DEBUG)
logger = logging.getLogger()
handler = logging.StreamHandler()
formatter = logging.Formatter(
        '%(asctime)s %(name)-12s %(levelname)-8s %(message)s')
handler.setFormatter(formatter)
logger.addHandler(handler)
logger.setLevel(logging.DEBUG)

try:
    params = config.config()
    # use our connection values to establish a connection
    conn = psycopg2.connect(
        host=params['pgsql']['host']
        , database=params['pgsql']['database']
        , user=params['pgsql']['user']
        , password=params['pgsql']['password']
    )

    currentTime = datetime.now()
    currMinuteTime_nonFormat = currentTime
    currMinuteTimeReplaceSeconds = currMinuteTime_nonFormat.replace(second=0, microsecond=0)

    # convert the times to EPOCH
    epochCurrTime = int(currMinuteTimeReplaceSeconds.timestamp())

    test = 123
    currMarket = sys.argv[1]
    currCoin = sys.argv[2]
    limit = int(sys.argv[3])
    tsGreaterThan = (epochCurrTime - (limit * 60)) + 1
    interval = int(sys.argv[4])
    timePeriod = int(sys.argv[5])
    sqlSelect = "SELECT id, closer, timestampintervalhigh from tkmcandlesticks"
    sqlWhere = "WHERE market = '%s' AND coin = '%s' AND timestampintervallow > %s" % (
    currMarket, currCoin, tsGreaterThan)
    ##
    # Order BY and limit statements were adversely impacting performance
    # sqlOrder = "ORDER BY timestampintervallow DESC"
    # sqlLimit = "LIMIT %s" % (limit)
    ##
    # Now we need to use numpy to order the data
    sqlStmnt = sqlSelect + " " + sqlWhere
    cursor = conn.cursor()
    cursor.execute(sqlStmnt)
    rows = cursor.fetchall()
    if len(rows) > 0:
        initialData = rows
        ##
        # Order the data
        ##
        idArrToOrder = [i[0] for i in initialData]
        indirectSort = numpy.argsort(idArrToOrder)
        intervalCalc = numpy.array([[initialData[j][1], initialData[j][2]] for j in indirectSort if j % interval == 0])
        ## Below is to calculate the minimum values in the given interval range
        # intervalCalc = Utilities.Utilities.calculatemingivenarange(initialData, indirectSort, interval)
        listForSMA = numpy.array([intervalCalc[k][0] for k in range(len(intervalCalc))])
        output = talib.SMA(listForSMA, timePeriod)
        cleanedList = [
            [
                intervalCalc[k][1], output[k]
            ] for k in range(len(output)) if not math.isnan(output[k])]
        print(cleanedList)
except Exception as e:
    print("Uh oh, you done fucked up - ")
    print(e)
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

    currMarket = sys.argv[1]
    currCoin = sys.argv[2]
    limit = int(sys.argv[3])
    tsGreaterThan = (epochCurrTime - (limit * 60)) + 1
    interval = int(sys.argv[4])
    timePeriod = int(sys.argv[5])
    sqlSelect = "SELECT id, high, low, closer, timestampintervalhigh from tkmcandlesticks"
    sqlWhere = "WHERE market = '%s' AND coin = '%s' AND timestampintervallow > %s" % (
        currMarket, currCoin, tsGreaterThan)
    ##
    # Order BY and limit statements were adversely impacting performance
    # sqlOrder = "ORDER BY timestampintervallow DESC"
    # sqlLimit = "LIMIT %s" % (limit)
    ##
    sqlStmnt = sqlSelect + " " + sqlWhere
    cursor = conn.cursor()
    cursor.execute(sqlStmnt)
    rows = cursor.fetchall()
    if len(rows) > 0:
        initialData = rows
        idArrToOrder = [i[0] for i in initialData]
        indirectSort = numpy.argsort(idArrToOrder)
        close = numpy.array([initialData[a][3] for a in indirectSort if a % interval == 0])
        high = numpy.array([initialData[b][1] for b in indirectSort if b % interval == 0])
        low = numpy.array([initialData[c][2] for c in indirectSort if c % interval == 0])
        timestamp = [initialData[d][4] for d in indirectSort if d % interval == 0]
        slowk, slowd = talib.STOCH(high, low, close, fastk_period=5, slowk_period=3, slowk_matype=0, slowd_period=3, slowd_matype=0)
        cleanedList = [
            [
                timestamp[j] , slowk[j], slowd[j], str("SlowK: %s | SlowD: %s"%(slowk[j], slowd[j]))
            ] for j in range(len(slowk)) if not math.isnan(slowk[j]) or not math.isnan(slowd[j]) ]
        print(cleanedList)


except Exception as e:
    print("Uh oh, you done fucked up: %s" % (e))
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
    currCoin = 'POT'
    currMarket = 'BTC'
    sqlSelect = "SELECT id, closer from tkmcandlesticks"
    sqlWhere = "WHERE market = '%s' AND coin = '%s'" % (currMarket, currCoin)
    sqlOrder = "ORDER BY timestampintervallow DESC"
    sqlLimit = "LIMIT 500"
    sqlStmnt = sqlSelect + " " + sqlWhere + " " + sqlOrder + " " + sqlLimit
    cursor = conn.cursor()
    cursor.execute(sqlStmnt)
    rows = cursor.fetchall()
    if len(rows) > 0:
        initialData = rows[::-1]
        close = numpy.array([item[1] for item in initialData])
        fiveMinuteCalc = numpy.array([close[j] for j in range(len(close)) if j % 5 == 0])
        outputRSI = talib.RSI(fiveMinuteCalc, 14)
        outputSMA = talib.SMA(fiveMinuteCalc, 14)
        cleanedList = [[j, outputRSI[j], outputSMA[j], str("RSI: %s | SMA: %s"%(outputRSI[j],outputSMA[j]))] for j in range(len(outputRSI)) if not math.isnan(outputRSI[j])]
        print(cleanedList)

        # print(sqlStmnt)
    # print ('Number of arguments: %s args' % (len(sys.argv)))
    # print ('Argument List:' + str(sys.argv))

except Exception as e:
    print("Uh oh, you done fucked up: %s" % (e))
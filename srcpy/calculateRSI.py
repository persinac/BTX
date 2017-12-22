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
    currMarket = sys.argv[1]
    currCoin = sys.argv[2]
    limit = int(sys.argv[3])
    interval = int(sys.argv[4])
    timePeriod = int(sys.argv[5])
    sqlSelect = "SELECT closer, timestampintervalhigh from tkmcandlesticks"
    sqlWhere = "WHERE market = '%s' AND coin = '%s'" % (currMarket, currCoin)
    sqlOrder = "ORDER BY timestampintervallow DESC"
    sqlLimit = "LIMIT %s" % (limit)
    sqlStmnt = sqlSelect + " " + sqlWhere + " " + sqlOrder + " " + sqlLimit
    cursor = conn.cursor()
    cursor.execute(sqlStmnt)
    rows = cursor.fetchall()
    if len(rows) > 0:
        initialData = rows[::-1]
        intervalCalc = numpy.array(
            [[initialData[j][0], initialData[j][1]] for j in range(len(initialData)) if j % interval == 0])
        listForRSI = numpy.array([intervalCalc[k][0] for k in range(len(intervalCalc))])
        outputRSI = talib.RSI(listForRSI, timePeriod)
        cleanedList = [
            [
                intervalCalc[k][1], outputRSI[k], str("RSI: %s"%(outputRSI[k]))
            ] for k in range(len(outputRSI)) if not math.isnan(outputRSI[k])]
        print(cleanedList)

except Exception as e:
    print("Uh oh, you done fucked up: %s" % (e))
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
    sqlSelect = "SELECT id, high, low, closer, timestampintervalhigh from tkmcandlesticks"
    sqlWhere = "WHERE market = '%s' AND coin = '%s'" % (currMarket, currCoin)
    sqlOrder = "ORDER BY timestampintervallow DESC"
    sqlLimit = "LIMIT %s" % (limit)
    sqlStmnt = sqlSelect + " " + sqlWhere + " " + sqlOrder + " " + sqlLimit
    cursor = conn.cursor()
    cursor.execute(sqlStmnt)
    rows = cursor.fetchall()
    if len(rows) > 0:
        initialData = rows[::-1]
        close = numpy.array([initialData[a][3] for a in range(len(initialData)) if a % interval == 0])
        high = numpy.array([initialData[b][1] for b in range(len(initialData)) if b % interval == 0])
        low = numpy.array([initialData[c][2] for c in range(len(initialData)) if c % interval == 0])
        timestamp = [initialData[d][4] for d in range(len(initialData)) if d % interval == 0]
        slowk, slowd = talib.STOCH(high, low, close, fastk_period=5, slowk_period=3, slowk_matype=0, slowd_period=3, slowd_matype=0)
        cleanedList = [
            [
                [
                    int(datetime.fromtimestamp(int(timestamp[j])).strftime('%H')),
                    int(datetime.fromtimestamp(int(timestamp[j])).strftime('%M')),
                    int(datetime.fromtimestamp(int(timestamp[j])).strftime('%S'))
                ]
                , slowk[j], slowd[j], str("SlowK: %s | SlowD: %s"%(slowk[j], slowd[j]))
            ] for j in range(len(slowk)) if not math.isnan(slowk[j]) or not math.isnan(slowd[j]) ]
        print(cleanedList)


except Exception as e:
    print("Uh oh, you done fucked up: %s" % (e))
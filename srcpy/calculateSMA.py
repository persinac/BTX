import time
import numpy
import talib
import psycopg2
import sys
import logging
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
    currCoin = 'ETH'
    currMarket = 'BTC'
    sqlSelect = "SELECT closer from tkmcandlesticks"
    sqlWhere = "WHERE market = '%s' AND coin = '%s'" % (currMarket, currCoin)
    sqlStmnt = sqlSelect + " " + sqlWhere
    cursor = conn.cursor()
    cursor.execute(sqlStmnt)
    rows = cursor.fetchall()
    if len(rows) > 0:
        close = numpy.array([row[0] for row in rows])
        print (close)
        output = talib.SMA(close, 14)
        print(output)
except Exception as e:
    print("Uh oh, you done fucked up")
    print(e)